<?php

namespace BoomCMS\Tests\Auth;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Auth\PermissionsProvider;
use BoomCMS\Core\Person\Person;
use BoomCMS\Core\Person\Guest;
use BoomCMS\Tests\AbstractTestCase;

class AuthenticateTest extends AbstractTestCase
{
    /**
     * @expectedException BoomCMS\Core\Auth\PersonNotFoundException
     */
    public function testPersonNotFoundExceptionIfInvalidEmail()
    {
        $session = $this->getMockSession();
        $personRepository = $this->getMockPersonRepository(['findByEmail']);
        $permissions = $this->getMock(PermissionsProvider::class);

        $email = 'test@test.com';
        $password = 'password';
        $auth = new Auth($session, $personRepository, $permissions);

        $personRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($this->equalTo($email))
            ->will($this->returnValue(new Guest()));

        $auth->authenticate($email, $password);
    }

    /**
     * @expectedException BoomCMS\Core\Auth\InvalidPasswordException
     */
    public function testInvalidPasswordExceptionIfInvalidPassword()
    {
        $session = $this->getMockSession();
        $personRepository = $this->getMockPersonRepository(['findByEmail', 'save']);
        $permissions = $this->getMock(PermissionsProvider::class);

        $email = 'test@test.com';
        $password = 'password';
        $auth = new Auth($session, $personRepository, $permissions);

        $person = $this->getMockBuilder(Person::class)
            ->setMethods(['checkPassword'])
            ->setConstructorArgs([['id' => 1, 'failed_logins' => 0]])
            ->getMock();

        $person
            ->expects($this->once())
            ->method('checkPassword')
            ->with($password)
            ->will($this->returnValue(false));

        $personRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($this->equalTo($email))
            ->will($this->returnValue($person));

        $auth->authenticate($email, $password);
    }

    public function testLoginCalledAndPersonReturnedIfCorrectDetails()
    {
        $session = $this->getMockSession();
        $personRepository = $this->getMockPersonRepository(['findByEmail']);
        $permissions = $this->getMock(PermissionsProvider::class);

        $email = 'test@test.com';
        $password = 'password';

        $auth = $this->getMockBuilder(Auth::class)
            ->setMethods(['login'])
            ->setConstructorArgs([$session, $personRepository, $permissions])
            ->getMock();

        $person = $this->getMockBuilder(Person::class)
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

        $personRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($this->equalTo($email))
            ->will($this->returnValue($person));

        $this->assertEquals($person, $auth->authenticate($email, $password));
    }
}
