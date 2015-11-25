<?php

namespace BoomCMS\Tests\Auth;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Auth\Guest;
use BoomCMS\Database\Models\Person;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AuthTest extends AbstractTestCase
{
    public function testGetPersonReturnsGuest()
    {
        $session = $this->getMockSession();
        $session
            ->expects($this->once())
            ->method('get')
            ->with($this->anything())
            ->will($this->returnValue(null));

        $permissions = $this->getMockPermissionsProvider();
        $repository = $this->getMockPersonRepository();

        $auth = new Auth($session, $repository, $permissions);
        $this->assertInstanceOf(Guest::class, $auth->getPerson());
    }

    public function testGetPersonReturnsPerson()
    {
        $personId = 1;

        $session = $this->getMockSession();
        $session
            ->expects($this->once())
            ->method('get')
            ->with($this->anything())
            ->will($this->returnValue($personId));

        $permissions = $this->getMockPermissionsProvider();

        $repository = $this->getMockPersonRepository(['find']);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo($personId))
            ->will($this->returnValue('person'));

        $auth = new Auth($session, $repository, $permissions);
        $this->assertEquals('person', $auth->getPerson());
    }

    public function testLogout()
    {
        $person = new Person([]);
        $session = $this->getMockSession();
        $permissions = $this->getMockPermissionsProvider();

        $auth = $this->getMockBuilder(Auth::class)
            ->setMethods(['getPerson', 'refreshRememberLoginToken'])
            ->setConstructorArgs([
                $session,
                $this->getMockPersonRepository(),
                $permissions,
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

        Cookie::shouldReceive('queue')
            ->once();

        Cookie::shouldReceive('forget')
            ->once()
            ->with('boomcms_autologin')
            ->andReturnSelf();

        $auth->logout();
        $this->assertFalse($auth->isLoggedIn());
    }

    public function testGetLoginSavesPersonIdToSession()
    {
        $person = new Person(['id' => 1]);
        $session = $this->getMockSession();
        $auth = new Auth($session, $this->getMockPersonRepository(), $this->getMockPermissionsProvider());

        $session
            ->expects($this->once())
            ->method('set')
            ->with($this->equalTo($auth->getSessionKey(), $person->getId()));

        $auth->login($person);
    }

    public function testLoginRememberIsCalled()
    {
        $person = new Person(['id' => 1]);

        $auth = $this->getMockBuilder(Auth::class)
            ->setMethods(['refreshRememberLoginToken', 'rememberLogin'])
            ->setConstructorArgs([$this->getMockSession(), $this->getMockPersonRepository(), $this->getMockPermissionsProvider()])
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
        $person = $this->getMockBuilder(Person::class)
            ->setMethods(['setRememberToken', 'getId'])
            ->setConstructorArgs([[]])
            ->getMock();

        $person
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $person
            ->expects($this->once())
            ->method('setRememberToken')
            ->with($this->anything());

        $personProvider = $this->getMockPersonRepository(['save']);
        $personProvider
            ->expects($this->once())
            ->method('save')
            ->with($this->equalTo($person));

        $auth = new Auth($this->getMockSession(),
            $personProvider,
            $this->getMockPermissionsProvider()
        );

        $auth->refreshRememberLoginToken($person);
    }

    public function testRememberLogin()
    {
        $person = new Person(['id' => 1, 'remember_token' => 'token']);

        $auth = $this->getMockBuilder(Auth::class)
            ->setConstructorArgs([
                $this->getMockSession(),
                $this->getMockPersonRepository(),
                $this->getMockPermissionsProvider(),
            ])
            ->setMethods(['saveRememberLoginToken'])
            ->getMock();

        Cookie::shouldReceive('queue')->once();
        Cookie::shouldReceive('forever')
            ->once()
            ->with($auth->getAutoLoginCookie(), $person->getId().'-'.$person->getRememberToken());

        $auth->rememberLogin($person);
    }

    public function testGetProvider()
    {
        $provider = $this->getMockPersonRepository();

        $auth = new Auth($this->getMockSession(),
            $provider,
            $this->getMockPermissionsProvider()
        );

        $this->assertEquals($provider, $auth->getProvider());
    }

    public function testAutoLoginSucceeds()
    {
        $person = new Person(['remember_token' => 'token']);
        $person->{Person::ATTR_ID} = 1;

        $provider = $this->getMockPersonRepository(['findByAutoLoginToken']);
        $request = $this->getMock(Request::class);

        $auth = $this->getMockBuilder(Auth::class)
            ->setConstructorArgs([
                $this->getMockSession(),
                $provider,
                $this->getMockPermissionsProvider(),
            ])
            ->setMethods(['login'])
            ->getMock();

        $request
            ->expects($this->once())
            ->method($this->equalTo('cookie'))
            ->with($auth->getAutoLoginCookie())
            ->will($this->returnValue('1-test'));

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
        $provider = $this->getMockPersonRepository(['findByAutoLoginToken']);

        $auth = $this->getMockBuilder(Auth::class)
            ->setConstructorArgs([
                $this->getMockSession(),
                $provider,
                $this->getMockPermissionsProvider(),
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
        $request = $this->getMock(Request::class);
        $provider = $this->getMockPersonRepository(['findByAutoLoginToken']);

        $auth = $this->getMockBuilder(Auth::class)
            ->setConstructorArgs([
                $this->getMockSession(),
                $provider,
                $this->getMockPermissionsProvider(),
            ])
            ->setMethods(['login'])
            ->getMock();

        $request
            ->expects($this->once())
            ->method($this->equalTo('cookie'))
            ->with($auth->getAutoLoginCookie())
            ->will($this->returnValue('1-test'));

        $provider
            ->expects($this->once())
            ->method('findByAutoLoginToken')
            ->with($this->equalTo('test'))
            ->will($this->returnValue(new Person([])));

        $auth->expects($this->never())
            ->method('login');

        $this->assertFalse($auth->autoLogin($request));
    }

    protected function getMockSession()
    {
        return $this
            ->getMockBuilder('Illuminate\Session\SessionManager')
            ->disableOriginalConstructor()
            ->setMethods(['remove', 'set', 'get'])
            ->getMock();
    }
}
