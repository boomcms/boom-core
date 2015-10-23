<?php

namespace BoomCMS\Tests\Person;

use BoomCMS\Core\Person\Person;
use BoomCMS\Tests\AbstractTestCase;
use Hautelook\Phpass\PasswordHash;

class PersonTest extends AbstractTestCase
{
    public function testLoadedIfHasId()
    {
        $person = new Person(['id' => 2]);

        $this->assertTrue($person->loaded());
    }

    public function testNotLoadedIfNoId()
    {
        $person = new Person([]);

        $this->assertFalse($person->loaded());
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

    public function testPersonIsLockedIfLockedUntilIsInTheFuture()
    {
        $person = new Person([]);
        $this->assertFalse($person->isLocked());

        $person = new Person(['locked_until' => time() - 10]);
        $this->assertFalse($person->isLocked());

        $person = new Person(['locked_until' => time() + 10]);
        $this->assertTrue($person->isLocked());
    }

    public function testIsValidIfLoadedAndNotLocked()
    {
        $person = new Person([]);
        $this->assertFalse($person->isValid(), 'No ID or locked_until');

        $person = new Person(['id' => 1, 'locked_until' => time() + 10]);
        $this->assertFalse($person->isValid(), 'Loaded but locked');

        $person = new Person(['locked_until' => time() - 10]);
        $this->assertFalse($person->isValid(), 'Not loaded, not locked');

        $person = new Person(['id' => 1, 'locked_until' => time() - 10]);
        $this->assertTrue($person->isValid(), 'Loaded and not locked');
    }
}
