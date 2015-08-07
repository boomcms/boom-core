<?php

use BoomCMS\Core\Auth\RandomPassword;

class Auth_RandomPasswordTest extends TestCase
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
