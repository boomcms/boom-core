<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Person;
use Hautelook\Phpass\PasswordHash;

class PersonTest extends AbstractModelTestCase
{
    protected $model = Person::class;

    public function testGetEmailReturnsEmailAttribute()
    {
        $email = 'test@test.com';
        $person = new Person([Person::ATTR_EMAIL => $email]);

        $this->assertEquals($email, $person->getEmail());
    }

    public function testIsSuperuserDefaultFalse()
    {
        $person = new Person([]);

        $this->assertFalse($person->isSuperuser());
    }

    public function testIsSuperuserReturnsTrue()
    {
        $person = new Person(['superuser' => true]);

        $this->assertTrue($person->isSuperuser());
    }

    public function testCheckPassword()
    {
        $hasher = new PasswordHash(8, false);
        $password = $hasher->HashPassword('test');

        $person = new Person(['password' => $password]);
        $this->assertTrue($person->checkPassword('test'));
        $this->assertFalse($person->checkPassword('test2'));
    }

    public function testSetGetRememberLoginToken()
    {
        $person = new Person([]);
        $person->setRememberToken('token');

        $this->assertEquals('token', $person->getRememberToken());
    }

    public function testSetEmailSetsEmailAddress()
    {
        $email = 'test@test.com';
        $person = new Person([]);
        $person->setEmail($email);

        $this->assertEquals($email, $person->getEmail());
    }

    public function testEmailAddressIsAlwaysLowecase()
    {
        $email = 'test@test.com';
        $person = new Person([]);
        $person->setEmail(strtoupper($email));

        $this->assertEquals($email, $person->getEmail());
    }

    public function testEmailAddressIsTrimmed()
    {
        $email = 'test@test.com';
        $person = new Person([]);
        $person->setEmail(' '.$email.' ');

        $this->assertEquals($email, $person->getEmail());
    }
}
