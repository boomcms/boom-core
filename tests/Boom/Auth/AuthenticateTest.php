<?php

use BoomCMS\Core\Auth;

use BoomCMS\Core\Person;

class Auth_AuthenticateTest extends TestCase
{
    /**
     * @expectedException BoomCMS\Core\Auth\PersonNotFoundException
     */
    public function testPersonNotFoundExceptionIfInvalidEmail()
    {
        $session = $this->getMockSession();
        $personProvider = $this->getMockBuilder('BoomCMS\Core\Person\Provider')
            ->setMethods(['findByEmail'])
            ->getMock();
        $permissions = $this->getMock('BoomCMS\Core\Auth\PermissionsProvider');

        $email = 'test@test.com';
        $password = 'password';
        $auth = new Auth\Auth($session, $personProvider, $permissions);

        $personProvider
            ->expects($this->once())
            ->method('findByEmail')
            ->with($this->equalTo($email))
            ->will($this->returnValue(new Person\Guest()));

        $auth->authenticate($email, $password);
    }

    /**
     * @expectedException BoomCMS\Core\Auth\PersonNotFoundException
     */
    public function testPersonNotFoundExceptionIfInvalidPassword()
    {
        $session = $this->getMockSession();
        $personProvider = $this->getMockBuilder('BoomCMS\Core\Person\Provider')
            ->setMethods(['findByEmail'])
            ->getMock();
        $permissions = $this->getMock('BoomCMS\Core\Auth\PermissionsProvider');

        $email = 'test@test.com';
        $password = 'password';
        $auth = new Auth\Auth($session, $personProvider, $permissions);

        $person = $this->getMockBuilder('BoomCMS\Core\Person\Person')
            ->setMethods(['checkPassword'])
            ->setConstructorArgs([['id' => 1]])
            ->getMock();

        $person
            ->expects($this->once())
            ->method('checkPassword')
            ->with($password)
            ->will($this->returnValue(false));

        $personProvider
            ->expects($this->once())
            ->method('findByEmail')
            ->with($this->equalTo($email))
            ->will($this->returnValue($person));

        $auth->authenticate($email, $password);
    }

    public function testLoginCalledAndPersonReturnedIfCorrectDetails()
    {
        $session = $this->getMockSession();
        $personProvider = $this->getMockBuilder('BoomCMS\Core\Person\Provider')
            ->setMethods(['findByEmail'])
            ->getMock();
        $permissions = $this->getMock('BoomCMS\Core\Auth\PermissionsProvider');

        $email = 'test@test.com';
        $password = 'password';

        $auth = $this->getMockBuilder('BoomCMS\Core\Auth\Auth')
            ->setMethods(['login'])
            ->setConstructorArgs([$session, $personProvider, $permissions])
            ->getMock();

        $person = $this->getMockBuilder('BoomCMS\Core\Person\Person')
            ->setMethods(['checkPassword'])
            ->setConstructorArgs([['id' => 1]])
            ->getMock();

        $auth
            ->expects($this->once())
            ->method('login')
            ->with($this->equalTo($person));

        $person
            ->expects($this->once())
            ->method('checkPassword')
            ->with($password)
            ->will($this->returnValue(true));

        $personProvider
            ->expects($this->once())
            ->method('findByEmail')
            ->with($this->equalTo($email))
            ->will($this->returnValue($person));

        $this->assertEquals($person, $auth->authenticate($email, $password));
    }
}