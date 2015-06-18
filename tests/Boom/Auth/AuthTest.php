<?php

use BoomCMS\Core\Auth;
use BoomCMS\Core\Person;
use Illuminate\Http\Request;

class Auth_AuthTest extends TestCase
{
    public function testLogout()
    {
		$person = new Person\Person([]);
        $session = $this->getMockSession();
        $cookie = $this->getMockCookieJar();
        $permissions = $this->getMockPermissionsProvider();

		$auth = $this->getMockBuilder('BoomCMS\Core\Auth\Auth')
			->setMethods(['getPerson', 'refreshRememberLoginToken'])
			->setConstructorArgs([
				$session,
				$this->getMockPersonProvider(),
				$permissions,
				$cookie
			])
			->getMock();

		$auth
			->expects($this->any())
			->method('getPerson')
			->will($this->returnValue($person));

		$auth
			->expects($this->once())
			->method('refreshRememberLoginToken')
			->with($person);

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
            ->setMethods(['refreshRememberLoginToken', 'rememberLogin'])
            ->setConstructorArgs([$this->getMockSession(), $this->getMockPersonProvider(), $this->getMockPermissionsProvider(), $this->getMockCookieJar()])
            ->getMock();

		$auth
			->expects($this->once())
			->method('refreshRememberLoginToken')
			->with($this->equalTo($person));

		$auth
			->expects($this->once())
			->method('rememberLogin')
			->with($this->equalTo($person));

        $auth->login($person, true);
    }

	public function testRefreshRememberLoginToken()
	{
        $person = $this->getMockBuilder('BoomCMS\Core\Person\Person')
            ->setMethods(['setRememberToken', 'loaded'])
            ->setConstructorArgs([[]])
            ->getMock();

		$person
			->expects($this->once())
			->method('loaded')
			->will($this->returnValue(true));

        $person
            ->expects($this->once())
            ->method('setRememberToken')
            ->with($this->anything());

        $personProvider = $this->getMockPersonProvider(['save']);
        $personProvider
            ->expects($this->once())
            ->method('save')
            ->with($this->equalTo($person));

        $auth = new Auth\Auth($this->getMockSession(),
			$personProvider,
			$this->getMockPermissionsProvider(),
			$this->getMockCookieJar()
		);

		$auth->refreshRememberLoginToken($person);
	}

    public function testRememberLogin()
    {
		$person = new Person\Person(['id' => 1, 'remember_token' => 'token']);
		$cookie = $this->getMockCookieJar();

		$auth = $this->getMockBuilder('BoomCMS\Core\Auth\Auth')
			->setConstructorArgs([
				$this->getMockSession(),
				$this->getMockPersonProvider(),
				$this->getMockPermissionsProvider(),
				$cookie
			])
			->setMethods(['saveRememberLoginToken'])
			->getMock();

        $cookie
            ->expects($this->once())
            ->method('forever')
            ->with($this->equalTo(
				$auth->getAutoLoginCookie()),
				$person->getId() . '-' . $person->getRememberToken()
			);

        $auth->rememberLogin($person);
    }

	public function testGetProvider()
	{
		$provider = $this->getMockPersonProvider();

		$auth = new Auth\Auth($this->getMockSession(),
			$provider,
			$this->getMockPermissionsProvider(),
			$this->getMockCookieJar()
		);

		$this->assertEquals($provider, $auth->getProvider());
	}

    public function testAutoLoginSucceeds()
    {
        $person = new Person\Person(['id' => 1, 'remember_token' => 'token']);
        $provider = $this->getMockPersonProvider(['findByAutoLoginToken']);
        $request = $this->getMock('Illuminate\Http\Request');

		$auth = $this->getMockBuilder('BoomCMS\Core\Auth\Auth')
			->setConstructorArgs([
				$this->getMockSession(),
				$provider,
				$this->getMockPermissionsProvider(),
				$this->getMockCookieJar()
			])
			->setMethods(['login'])
			->getMock();

        $request
            ->expects($this->once())
            ->method($this->equalTo('cookie'))
            ->with($auth->getAutoLoginCookie())
            ->will($this->returnValue('test'));

        $provider
            ->expects($this->once())
            ->method('findByAutoLoginToken')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($person));

        $auth->expects($this->once())
            ->method('login')
            ->with($this->equalTo($person));

        $this->assertEquals($person, $auth->autoLogin($request));
    }

    public function testAutoLoginFailsWhenNoCookie()
    {
        $request = $this->getMock('Illuminate\Http\Request');
        $provider = $this->getMockPersonProvider(['findByAutoLoginToken']);

		$auth = $this->getMockBuilder('BoomCMS\Core\Auth\Auth')
			->setConstructorArgs([
				$this->getMockSession(),
				$provider,
				$this->getMockPermissionsProvider(),
				$this->getMockCookieJar()
			])
			->setMethods(['login'])
			->getMock();

        $request
            ->expects($this->once())
            ->method($this->equalTo('cookie'))
            ->with($auth->getAutoLoginCookie())
            ->will($this->returnValue(null));

        $provider
            ->expects($this->never())
            ->method('findByAutoLoginToken');

        $auth->expects($this->never())
            ->method('login');

        $this->assertFalse($auth->autoLogin($request));
    }

    public function testAutoLoginFailsWithInvalidToken()
    {
		$request = $this->getMock('Illuminate\Http\Request');
        $provider = $this->getMockPersonProvider(['findByAutoLoginToken']);

		$auth = $this->getMockBuilder('BoomCMS\Core\Auth\Auth')
			->setConstructorArgs([
				$this->getMockSession(),
				$provider,
				$this->getMockPermissionsProvider(),
				$this->getMockCookieJar()
			])
			->setMethods(['login'])
			->getMock();

        $request
            ->expects($this->once())
            ->method($this->equalTo('cookie'))
            ->with($auth->getAutoLoginCookie())
            ->will($this->returnValue('test'));

        $provider
            ->expects($this->once())
            ->method('findByAutoLoginToken')
            ->with($this->equalTo('test'))
            ->will($this->returnValue(new Person\Person([])));

        $auth->expects($this->never())
            ->method('login');

        $this->assertFalse($auth->autoLogin($request));
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