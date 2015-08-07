<?php

use BoomCMS\Core\Person;

class Person_ProviderTest extends TestCase
{
    /**
     * @expectedException BoomCMS\Core\Person\DuplicateEmailException
     */
    public function testCreateThrowsDuplicateEmailException()
    {
        $email = 'test@test.com';
        $person = new Person\Person(['id' => 1, 'email' => $email]);

        $provider = $this->getMockBuilder('BoomCMS\Core\Person\Provider')
            ->setMethods(['findByEmail'])
            ->getMock();

        $provider
            ->expects($this->once())
            ->method('findByEmail')
            ->with($this->equalTo($email))
            ->will($this->returnValue($person));

        $provider->create(['name' => 'test', 'email' => $email]);
    }
}
