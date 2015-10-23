<?php

namespace BoomCMS\Tests\Person;

use BoomCMS\Core\Person;
use BoomCMS\Tests\AbstractTestCase;

class ProviderTest extends AbstractTestCase
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
