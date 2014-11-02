<?php

namespace Boom\Auth;

use \Cookie as Cookie;
use \DB as DB;
use \PasswordHash;
use \Session;
use \Model_Role as Role;

use Boom\Config;
use \Boom\Person;

class Auth
{
    /**
	 *
	 * @var array
	 */
    protected $config;

    protected static $instance;

    /**
	 *
	 * @var Boom\Person\Person
	 */
    protected $person;

    /**
	 *
	 * @var type Session
	 */
    protected $session;

    protected $sessionKey = 'boomPersonId';

    protected $permissions_cache = array();

    public function __construct($config = array(), Session $session)
    {
        $this->config = $config;
        $this->session = $session;
    }

    public function logout()
    {
        $this->session->delete('auth_forced');

        if ($token = Cookie::get('authautologin')) {
            // Delete the autologin cookie to prevent re-login
            Cookie::delete('authautologin');

            // Clear the autologin token from the database
            $token = ORM::factory('User_Token', array('token' => $token));

            if ($token->loaded() and $logout_all) {
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
            $token = ORM::factory('User_Token', array('token' => $token));

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
        if ($this->isDisabled()) {
            return true;
        }

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
                $role = new Role(array('name' => $role));
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

    protected function _login(Person\Person $person, $password = null, $remember = false)
    {
        $this->person = $person;

        /**
		 * Although it's slower, we the check password first before checking that the account is valid and not locked.
		 * It shouldn't cause too much of a time waste for genuine users but may slow down hack attempts.
		 */
        if ($this->check_password($password) && $this->person->loaded() && $this->person->isEnabled() && ! $this->person->isLocked()) {
            $this->complete_login($this->person);
            $remember === true && $this->_remember_login();

            return true;
        } elseif ($this->person->loaded() && ! $this->person->isLocked()) {
            $this->person->loginFailed();

            return false;
        }
    }

    public function cache_permissions($page)
    {
        $permissions = DB::select('roles.name', array('page_mptt.id', 'page_id'), array(DB::expr("bit_and(allowed)"), 'allowed'))
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

    public function complete_login($person)
    {
        // Store the person ID in the session data.
        $this->session->set($this->sessionKey, $person->getId());
    }

    public function force_login($person, $mark_as_forced = false)
    {
        $this->person = $person;

        return $this->complete_login($this->person);
    }

    public function hash($password)
    {
        if ( ! class_exists('PasswordHash')) {
            require \Kohana::find_file('vendor', 'PasswordHash');
        }

        $hasher = new PasswordHash(8, false);

        return $hasher->HashPassword($password);
    }

    public function getPerson()
    {
        if ($this->person === null) {
            $personId = $this->session->get($this->sessionKey);

            $this->person = $personId ? Person\Factory::byId($personId) : new Person\Guest();
        }

        return $this->person;
    }

    public static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static(Config::get('auth'), Session::instance());
        }

        return static::$instance;
    }

    public function isLoggedIn()
    {
        if ($this->isDisabled()) {
            return true;
        }

        return ! $this->getPerson() instanceof Person\Guest;
    }

    public function isDisabled()
    {
        return isset($this->config['disabled']) && $this->config['disabled'] === true;
    }

    public function login(Person\Person $person, $password, $remember = false)
    {
        if (! $password) {
            return false;
        }

        if ( ! is_object($person) && ! $person instanceof Person\Person) {
            // If we haven't been called with a person object then assume it's an email address
            // and get the person from the database.
            $person = new Model_Person(array('email' => $person));
        }

        return $this->_login($person, $password, $remember);
    }

    protected function _remember_login()
    {
        // Token data
        $data = array(
            'user_id'    => $this->getPerson()->getId(),
            'expires'    => time() + $this->_config['lifetime'],
            'user_agent' => sha1(Request::$user_agent),
        );

        // Create a new autologin token
        $token = ORM::factory('User_Token')
            ->values($data)
            ->create();

        // Set the autologin cookie
        Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
    }

    public function check_password($password)
    {
        if ( ! class_exists('PasswordHash')) {
            require \Kohana::find_file('vendor', 'PasswordHash');
        }

        $hasher = new PasswordHash(8, false);

        /*
		 * Create a dummy password to compare against if the user doesn't exist.
		 * This wastes CPU time to protect against probing for valid usernames.
		 */
        $hash = ($this->person->getPassword()) ? $this->person->getPassword() : '$2a$08$1234567890123456789012';

        return $hasher->CheckPassword($password, $hash) && $this->person->loaded();
    }
}
