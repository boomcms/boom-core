<?php

use BoomCMS\Core\Person\Person;

class Person_PersonTest extends TestCase
{
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
}