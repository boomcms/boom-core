<?php

namespace BoomCMS\Core\Auth;

use \Cookie as Cookie;

use BoomCMS\Core\Person;

use Hautelook\Phpass\PasswordHash;
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
     * @throws PersonNotFoundException
     * @throws PersonSuspendedException
     */
    public function authenticate($email, $password, $remember = false)
    {
        $person = $this->personProvider->findByEmail(trim($email));

        if ( ! $person->checkPassword($password) ||  ! $person->loaded()) {
            throw new PersonNotFoundException();
        }

        if ($person->isLocked()) {
            throw new PersonLockedException();
        }

        $this->login($person, $remember);

        return $person;
    }

    public function logout()
    {
        $this->session->delete($this->sessionKey);
        $this->session->regenerate();

        $this->person = new Person\Guest();

        return $this;
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
