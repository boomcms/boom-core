<?php

use BoomCMS\Core\Person\Person;

class LoggedInTest extends TestCase
{
    public function testLoggedInForSuperuserDoesntCheckPermissions()
    {
        $person = new Person(['id' => 1, 'superuser' => true]);
        $permissionsProvider = $this->getMock('BoomCMS\Core\Auth\PermissionsProvider');

        $permissionsProvider
            ->expects($this->never())
            ->method('lookup');

        $auth = $this->getAuth($person, $permissionsProvider);

        $auth
            ->expects($this->any())
            ->method('IsLoggedIn')
            ->will($this->returnValue(true));

        $this->assertTrue($auth->loggedIn('testrole'));
    }

    public function testLoggedInForNonSuperuserDoesCheckPermissions()
    {
        $person = new Person(['id' => 1]);
        $permissionsProvider = $this->getMock('BoomCMS\Core\Auth\PermissionsProvider');

        $permissionsProvider
            ->expects($this->once())
            ->method('lookup')
            ->with($this->equalTo($person), $this->equalTo('testrole'));

        $auth = $this->getAuth($person, $permissionsProvider);

        $auth
            ->expects($this->any())
            ->method('IsLoggedIn')
            ->will($this->returnValue(true));

        $auth->loggedIn('testrole');
    }

    public function testLoggedInWithRoleWhileLoggedOutReturnsFalse()
    {
        $person = new Person(['id' => 1, 'superuser' => true]);
        $permissionsProvider = $this->getMock('BoomCMS\Core\Auth\PermissionsProvider');

        $permissionsProvider
            ->expects($this->never())
            ->method('lookup');

        $auth = $this->getAuth($person, $permissionsProvider);

        $auth
            ->expects($this->any())
            ->method('IsLoggedIn')
            ->will($this->returnValue(false));

        $this->assertFalse($auth->loggedIn('testrole'));
    }

    public function testLoggedInWithoutPermissionReturnsFalse()
    {
        $person = new Person(['id' => 1]);
        $permissionsProvider = $this->getMock('BoomCMS\Core\Auth\PermissionsProvider');

        $permissionsProvider
            ->expects($this->once())
            ->method('lookup')
            ->with($this->equalTo($person), $this->equalTo('testrole'))
            ->will($this->returnValue(false));

        $auth = $this->getAuth($person, $permissionsProvider);

        $auth
            ->expects($this->any())
            ->method('IsLoggedIn')
            ->will($this->returnValue(true));

        $this->assertFalse($auth->loggedIn('testrole'));
    }

    public function testLoggedInWithPermissionReturnsTrue()
    {
        $person = new Person(['id' => 1]);
        $permissionsProvider = $this->getMock('BoomCMS\Core\Auth\PermissionsProvider');

        $permissionsProvider
            ->expects($this->once())
            ->method('lookup')
            ->with($this->equalTo($person), $this->equalTo('testrole'))
            ->will($this->returnValue(true));

        $auth = $this->getAuth($person, $permissionsProvider);

        $auth
            ->expects($this->any())
            ->method('IsLoggedIn')
            ->will($this->returnValue(true));

        $this->assertTrue($auth->loggedIn('testrole'));
    }

    protected function getAuth($person, $permissionsProvider)
    {
        $auth = $this->getMockBuilder('BoomCMS\Core\Auth\Auth')
            ->setConstructorArgs([$this->getMockSession(), $this->getMockPersonProvider(), $permissionsProvider])
            ->setMethods(['isLoggedIn', 'getPerson'])
            ->getMock();

        $auth
            ->expects($this->any())
            ->method('getPerson')
            ->will($this->returnValue($person));

        return $auth;
    }
}
