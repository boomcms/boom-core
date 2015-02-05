<?php

namespace Boom\Auth;

use \Cookie as Cookie;
use \DB as DB;
use \Model_Role as Role;

use Hautelook\Phpass\PasswordHash;
use Boom\Person;

use Cartalyst\Sentry\Users\ProviderInterface;
use Cartalyst\Sentry\Users\UserNotFoundException;
use \Session;

class Auth
{
    protected static $instance;

    /**
	 *
	 * @var Boom\Person\Person
	 */
    protected $person;

    /**
     *
     * @var Person\Provider
     */
    private $personProvider;

    /**
	 *
	 * @var type Session
	 */
    protected $session;

    protected $sessionKey = 'boomPersonId';

    protected $permissions_cache = [];

    public function __construct(Session $session, ProviderInterface $personProvider = null)
    {
        $this->session = $session;
        $this->personProvider = $personProvider?: new Person\Provider();
    }

    public function authenticate($email, $password, $remember = false)
    {
        $person = $this->personProvider->findByCredentials([
            'email' => $email,
            'password' => $this->hash($password),
        ]);

        if ( ! $person->isValid()) {
            throw new UserNotFoundException;
        }

        $this->login($person, $remember);
    }

    public function logout()
    {
        $this->session->delete('auth_forced');

        if ($token = Cookie::get('authautologin')) {
            // Delete the autologin cookie to prevent re-login
            Cookie::delete('authautologin');

            // Clear the autologin token from the database
            $token = ORM::factory('User_Token', ['token' => $token]);

            if ($token->loaded()) {
                // Delete all user tokens. This isn't the most elegant solution but does the job
                $tokens = ORM::factory('User_Token')->where('user_id','=',$token->user_id)->find_all();

                foreach ($tokens as $_token) {
                    $_token->delete();
                }
            } elseif ($token->loaded()) {
                $token->delete();
            }
        }

        // Destroy the session completely
        $this->session->destroy();

        // Remove the user from the session
        $this->session->delete($this->sessionKey);

        // Regenerate session_id
        $this->session->regenerate();

        $this->person = new Person\Guest();

        // Double check
        return ! $this->isLoggedIn();
    }

    public function auto_login()
    {
        if ($token = Cookie::get('authautologin')) {
            // Load the token and user
            $token = ORM::factory('User_Token', ['token' => $token]);

            if ($token->loaded() and $token->user->loaded()) {
                if ($token->user_agent === sha1(Request::$user_agent)) {
                    // Save the token to create a new unique token
                    $token->save();

                    // Set the new token
                    Cookie::set('authautologin', $token->token, $token->expires - time());

                    // Complete the login with the found data
                    $this->complete_login($token->user);

                    // Automatic login was successful
                    return $token->user;
                }

                // Token is invalid
                $token->delete();
            }
        }

        return FALSE;
    }

    public function loggedIn($role = null, $page = null)
    {
        $person = $this->getPerson();

        if ($role === null) {
            return $this->isLoggedIn();
        } else {
            // A role has been given - check whether the active person is allowed to perform the role.

            /**
			 * If a page has been given then add 'p_' to the role name.
			 *
			 * Roles which are applied to pages are prefixed with 'p_' in the database
			 * so that we can display them seperately from the general permissions when adding roles to groups in the people manager.
			 *
			 * To avoid having to add p_ to the start of role names everywhere in the code we just add the prefix here.
			 */
            if ($page !== NULL and is_string($role)) {
                $role = 'p_'.$role;
            }

            if (is_string($role)) {
                $role = new Role(['name' => $role]);
            }

            // Does the person have the role at the specified page?
            $page_id = ($page) ? $page->getId() : 0;
            $cache_key = md5($role.$page_id);

            if ( ! isset($this->_permissions_cache[$cache_key])) {
                $this->_permissions_cache[$cache_key] = $page ? $this->getPerson()->hasPagePermission($role, $page) : $this->getPerson()->hasPermission($role);
            }

            return $this->_permissions_cache[$cache_key];
        }
    }

    public function cache_permissions($page)
    {
        $permissions = DB::select('roles.name', ['page_mptt.id', 'page_id'], [DB::expr("bit_and(allowed)"), 'allowed'])
            ->from('people_roles')
            ->where('person_id', '=', $this->getPerson()->getId())
            ->join('roles', 'inner')
            ->on('people_roles.role_id', '=', 'roles.id')
            ->group_by('role_id')
            ->join('page_mptt', 'left')
            ->on('people_roles.page_id', '=', 'page_mptt.id')
            ->or_where_open()
                ->and_where_open()
                    ->where('lft', '<=', $page->getMptt()->lft)
                    ->where('rgt', '>=', $page->getMptt()->rgt)
                    ->where('scope', '=', $page->getMptt()->scope)
                ->and_where_close()
                ->or_where('people_roles.page_id', '=', null)
            ->or_where_close()
            ->execute()
            ->as_array();

        foreach ($permissions as $p) {
            $this->permissions_cache[md5($p['name']. (int) $p['page_id'])] = $p['allowed'];
        }

        return $this;
    }

    public function login(Person\Person $person, $remember = false)
    {
        $this->person = $person;

        $this->session->set($this->sessionKey, $person->getId());
    }

    public function hash($password)
    {
        $hasher = new PasswordHash(8, false);

        return $hasher->HashPassword($password);
    }

    public function getPerson()
    {
        if ($this->person === null) {
            $personId = $this->session->get($this->sessionKey);

            $this->person = $personId ? $this->personProvider->findById($personId) : $this->personProvider->getEmptyUser();
        }

        return $this->person;
    }

    public function isLoggedIn()
    {
        return $this->getPerson()->isValid();
    }

    protected function _remember_login()
    {
        // Token data
        $data = [
            'user_id'    => $this->getPerson()->getId(),
            'expires'    => time() + $this->_config['lifetime'],
            'user_agent' => sha1(Request::$user_agent),
        ];

        // Create a new autologin token
        $token = ORM::factory('User_Token')
            ->values($data)
            ->create();

        // Set the autologin cookie
        Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
    }
}
