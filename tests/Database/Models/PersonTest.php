<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Person;

class PersonTest extends AbstractModelTestCase
{
    protected $model = Person::class;

    public function testAddSite()
    {
        $this->markTestIncomplete();
    }

    public function testGetAuthIdentifier()
    {
        $person = new Person();
        $person->id = 1;

        $this->assertEquals($person->id, $person->getAuthIdentifier());
    }

    public function testGetAuthPassword()
    {
        $person = new Person(['password' => 'test']);

        $this->assertEquals($person->password, $person->getAuthPassword());
    }

    public function testGetEmailReturnsEmailAttribute()
    {
        $email = 'test@test.com';
        $person = new Person([Person::ATTR_EMAIL => $email]);

        $this->assertEquals($email, $person->getEmail());
    }

    public function testGetRememberTokenName()
    {
        $person = new Person();

        $this->assertEquals(Person::ATTR_REMEMBER_TOKEN, $person->getRememberTokenName());
    }

    public function testGetSites()
    {
        $this->markTestIncomplete();
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

    public function testRemoveSite()
    {
        $this->markTestIncomplete();
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
