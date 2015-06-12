<?php

namespace BoomCMS\Core\Auth;

use BoomCMS\Core\Person;

use Hautelook\Phpass\PasswordHash;
use Illuminate\Session\SessionManager as Session;
use Illuminate\Contracts\Cookie\QueueingFactory as Cookie;

class Auth
{
    /**
     *
     * @var string
     */
    protected $autoLoginCookie = 'boomcms_autologin';

    /**
     *
     * @var Cookie
     */
    protected $cookie;

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

    public function __construct(Session $session,
        Person\Provider $personProvider,
        PermissionsProvider $permissionsProvider,
        Cookie $cookie)
    {
        $this->session = $session;
        $this->personProvider = $personProvider;
        $this->permissionsProvider = $permissionsProvider;
        $this->cookie = $cookie;
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
	
	public function autoLogin()
	{
		
	}

    public function getAutoLoginCookie()
    {
        return $this->autoLoginCookie;
    }

    public function getPerson()
    {
        if ($this->person === null) {
            $personId = $this->session->get($this->getSessionKey());

            $this->person = $personId ?
                $this->personProvider->findById($personId)
                : $this->personProvider->getEmptyUser();
        }

        return $this->person;
    }
	
	/**
	 * 
	 * @return Person\Provider
	 */
	public function getProvider()
	{
		return $this->personProvider;
	}

    public function getSessionKey()
    {
        return $this->sessionKey;
    }

    public function hash($password)
    {
        $hasher = new PasswordHash(8, false);

        return $hasher->HashPassword($password);
    }

    public function isLoggedIn()
    {
        return $this->getPerson()->isValid();
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
        $this->session->set($this->getSessionKey(), $person->getId());

        if ($remember) {
			$this->refreshRememberLoginToken($person);
            $this->rememberLogin($person);
        }
    }

    public function logout()
    {
		$this->refreshRememberLoginToken($this->getPerson());
		
        $this->session->remove($this->getSessionKey());
        $this->cookie->queue($this->cookie->forget($this->getAutoLoginCookie()));

        $this->person = new Person\Guest();

        return $this;
    }
	
	public function refreshRememberLoginToken(Person\Person $person)
	{
		if ($person->loaded()) {
			$token = str_random(60);
			$person->setRememberToken($token);
			$this->personProvider->save($person);
		}
		
		return $this;
	}

    public function rememberLogin(Person\Person $person)
    {
		$value = $person->getId() . '-' . $person->getRememberToken();

        $this->cookie->queue(
			$this->cookie->forever($this->getAutoLoginCookie(), $value)
        );
    }
}
