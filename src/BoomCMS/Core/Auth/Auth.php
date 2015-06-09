<?php

namespace BoomCMS\Core\Auth;

use \Cookie as Cookie;

use BoomCMS\Core\Person;

use Hautelook\Phpass\PasswordHash;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Illuminate\Session\SessionManager as Session;

class Auth
{
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
     * @var PermissionProvider
     */
    private $permissionsProvider;

    /**
	 *
	 * @var type Session
	 */
    protected $session;

    protected $sessionKey = 'boomcms.person.id';

    public function __construct(Session $session, Person\Provider $personProvider, PermissionsProvider $permissionsProvider)
    {
        $this->session = $session;
        $this->personProvider = $personProvider;
        $this->permissionsProvider = $permissionsProvider;
    }

    /**
     *
     * @param  type                  $email
     * @param  type                  $password
     * @param  type                  $remember
     * @return Person\Person
     * @throws UserNotFoundException
     */
    public function authenticate($email, $password, $remember = false)
    {
        $person = $this->personProvider->findByEmail(trim($email));

       // if ($person->isValid() && $person->checkPassword($password)) {
            $this->login($person, $remember);

            return $person;
      //      }

        if ( ! $person->isValid()) {
            throw new UserNotFoundException();
        }

        if ($person->isLocked()) {

        }
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
        return ($role === null)
            ? $this->isLoggedIn()
            : $this->isLoggedIn() && ($this->getPerson()->isSuperuser() || $this->permissionsProvider->lookup($this->getPerson(), $role, $page));
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
