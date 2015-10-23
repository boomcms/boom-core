<?php

namespace BoomCMS\Tests\Auth;

use BoomCMS\Core\Auth\RandomPassword;
use BoomCMS\Tests\AbstractTestCase;

class RandomPasswordTest extends AbstractTestCase
{
    public function testGetPasswordReturnsString()
    {
        $password = new RandomPassword();

        $this->assertInternalType('string', $password->getPassword());
    }

    public function testGetPasswordAsString()
    {
        $password = new RandomPassword();

        $this->assertInternalType('string', (string) $password);
    }
}
