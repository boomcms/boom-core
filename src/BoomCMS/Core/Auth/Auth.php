<?php

namespace BoomCMS\Core\Auth;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\Person;
use BoomCMS\Repositories\Person as PersonRepository;
use Hautelook\Phpass\PasswordHash;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager as Session;
use Illuminate\Support\Facades\Cookie;

class Auth
{
    /**
     * @var string
     */
    protected $autoLoginCookie = 'boomcms_autologin';

    /**
     * Amount of time to lock an account for after too many failed logins.
     *
     * @var int
     */
    protected $lockWait = 900;

    /**
     * @var Person
     */
    protected $person;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var PermissionProvider
     */
    private $permissionsProvider;

    /**
     * @var type Session
     */
    protected $session;

    protected $sessionKey = 'boomcms.person.id';

    public function __construct(Session $session,
        PersonRepository $personRepository,
        PermissionsProvider $permissionsProvider)
    {
        $this->session = $session;
        $this->personRepository = $personRepository;
        $this->permissionsProvider = $permissionsProvider;
    }

    /**
     * @param type $email
     * @param type $password
     * @param type $remember
     *
     * @throws PersonNotFoundException
     * @throws PersonSuspendedException
     *
     * @return Person
     */
    public function authenticate($email, $password, $remember = false)
    {
        $person = $this->personRepository->findByEmail(trim($email));

        if ($person && $person->isLocked()) {
            throw new PersonLockedException($person->getLockedUntil());
        }

        if (!$person || !$person->checkPassword($password)) {
            if ($person) {
                $this->loginFailed($person);

                throw new InvalidPasswordException($person);
            }

            throw new PersonNotFoundException();
        }

        $this->login($person, $remember);

        return $person;
    }

    public function autoLogin(Request $request)
    {
        $token = $request->cookie($this->getAutoLoginCookie());

        if ($token) {
            list($personId, $token) = explode('-', $token);
            $person = $this->personRepository->findByAutoLoginToken($token);

            if ($person && $personId && $person->getId() == $personId) {
                $this->login($person);

                return $person;
            }
        }

        return false;
    }

    /**
     * Determines whether the current user can delete a given page.
     *
     * @param Page $page
     *
     * @return bool
     */
    public function canDelete(Page $page)
    {
        return $page->wasCreatedBy($this->getPerson())
            || $this->loggedIn('delete_page', $page)
            || $this->loggedIn('manage_pages');
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
                $this->personRepository->find($personId)
                : $this->getEmptyUser();
        }

        return $this->person;
    }

    /**
     * @return Guest
     */
    public function getEmptyUser()
    {
        return new Guest();
    }

    /**
     * @return PersonRepository
     */
    public function getProvider()
    {
        return $this->personRepository;
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
        return $this->getPerson()->getId() > 0;
    }

    public function loggedIn($role = null, $page = null)
    {
        return ($role === null)
            ? $this->isLoggedIn()
            : $this->isLoggedIn() && ($this->getPerson()->isSuperuser() || $this->permissionsProvider->lookup($this->getPerson(), $role, $page));
    }

    public function login(Person $person, $remember = false)
    {
        $this->person = $person;
        $this->session->set($this->getSessionKey(), $person->getId());

        if ($remember) {
            $this->refreshRememberLoginToken($person);
            $this->rememberLogin($person);
        }
    }

    public function loginFailed(Person $person)
    {
        if ($person->getLastFailedLogin()->getTimestamp() > (time() - 600)) {
            $person->incrementFailedLogins();

            if ($person->getFailedLogins() > 10) {
                $person->setLockedUntil(time() + $this->lockWait);
            }
        } else {
            $person->setFailedLogins(1);
        }

        $person->setLastFailedLogin(time());
        $this->personRepository->save($person);
    }

    public function logout()
    {
        $this->refreshRememberLoginToken($this->getPerson());

        $this->session->remove($this->getSessionKey());
        Cookie::queue(Cookie::forget($this->getAutoLoginCookie()));

        $this->person = new Guest();

        return $this;
    }

    public function refreshRememberLoginToken(Person $person)
    {
        if ($person->getId() > 0) {
            $token = str_random(60);
            $person->setRememberToken($token);
            $this->personRepository->save($person);
        }

        return $this;
    }

    public function rememberLogin(Person $person)
    {
        $value = $person->getId().'-'.$person->getRememberToken();

        Cookie::queue(Cookie::forever($this->getAutoLoginCookie(), $value));
    }
}
