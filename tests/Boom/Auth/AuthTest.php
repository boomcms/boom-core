<?php

use BoomCMS\Core\Auth;

use BoomCMS\Core\Person;

class Auth_AuthTest extends TestCase
{
    public function testLogout()
    {
        $session = $this->getMockSession();
        $cookie = $this->getMockCookieJar();
        $permissions = $this->getMockPermissionsProvider();

        $auth = new Auth\Auth($session, $this->getMockPersonProvider(), $permissions, $cookie);

        $session
            ->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($auth->getSessionKey()));

        $cookie
            ->expects($this->once())
            ->method('forget')
            ->with($this->equalTo('boomcms_autologin'));

        $auth->logout();

        $this->assertFalse($auth->isLoggedIn());
        $this->assertInstanceOf('BoomCMS\Core\Person\Guest', $auth->getPerson());
    }

    public function testGetLoginSavesPersonIdToSession()
    {
        $person = new Person\Person(['id' => 1]);
        $session = $this->getMockSession();
        $auth = new Auth\Auth($session, $this->getMockPersonProvider(), $this->getMockPermissionsProvider(), $this->getMockCookieJar());

        $session
            ->expects($this->once())
            ->method('set')
            ->with($this->equalTo($auth->getSessionKey(), $person->getId()));

        $auth->login($person);
    }

    public function testLoginRememberIsCalled()
    {
        $person = new Person\Person(['id' => 1]);

        $auth = $this->getMockBuilder('BoomCMS\Core\Auth\Auth')
            ->setMethods(['rememberLogin'])
            ->setConstructorArgs([$this->getMockSession(), $this->getMockPersonProvider(), $this->getMockPermissionsProvider(), $this->getMockCookieJar()])
            ->getMock();

        $auth->login($person, true);
    }

    public function testRememberLogin()
    {
        $person = $this->getMockBuilder('BoomCMS\Core\Person\Person')
            ->setConstructorArgs([[]])
            ->getMock();

        $person
            ->expects($this->once())
            ->method('setRememberToken')
            ->with($this->anything());

        $personProvider = $this->getMockPersonProvider();
        $personProvider
            ->expects($this->once())
            ->method('save')
            ->with($this->equalTo($person));

        $cookie = $this->getMockCookieJar();

        $auth = $this->getMockBuilder('BoomCMS\Core\Auth\Auth')
            ->setMethods(['rememberLogin'])
            ->setConstructorArgs([$this->getMockSession(), $personProvider, $this->getMockPermissionsProvider(), $cookie])
            ->getMock();

        $cookie
            ->expects($this->once())
            ->method('forever')
            ->with($this->equalTo($auth->getAutoLoginCookie()), $this->anything());

        $auth->rememberLogin($person);
    }

    protected function getMockSession()
    {
        return $this
            ->getMockBuilder('Illuminate\Session\SessionManager')
            ->disableOriginalConstructor()
            ->setMethods(['remove', 'set'])
            ->getMock();
    }
}