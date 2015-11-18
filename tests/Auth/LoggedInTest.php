<?php

namespace BoomCMS\Tests\Auth;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Auth\PermissionsProvider;
use BoomCMS\Database\Models\Person;
use BoomCMS\Tests\AbstractTestCase;

class LoggedInTest extends AbstractTestCase
{
    public function testLoggedInForSuperuserDoesntCheckPermissions()
    {
        $person = new Person(['id' => 1, 'superuser' => true]);
        $permissionsProvider = $this->getMock(PermissionsProvider::class);

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
        $permissionsProvider = $this->getMock(PermissionsProvider::class);

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
        $permissionsProvider = $this->getMock(PermissionsProvider::class);

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
        $permissionsProvider = $this->getMock(PermissionsProvider::class);

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
        $permissionsProvider = $this->getMock(PermissionsProvider::class);

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
        $auth = $this->getMockBuilder(Auth::class)
            ->setConstructorArgs([$this->getMockSession(), $this->getMockPersonRepository(), $permissionsProvider])
            ->setMethods(['isLoggedIn', 'getPerson'])
            ->getMock();

        $auth
            ->expects($this->any())
            ->method('getPerson')
            ->will($this->returnValue($person));

        return $auth;
    }
}
