<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Person;
use BoomCMS\Repositories\Person as PersonRepository;
use BoomCMS\Tests\AbstractTestCase;

class PersonTest extends AbstractTestCase
{
    /**
     * @expectedException BoomCMS\Exceptions\DuplicateEmailException
     */
    public function testCreateThrowsDuplicateEmailException()
    {
        $email = 'test@test.com';
        $person = new Person(['id' => 1, 'email' => $email]);

        $provider = $this->getMockBuilder(PersonRepository::class)
            ->disableOriginalConstructor()
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
