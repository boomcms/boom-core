<?php

namespace BoomCMS\Tests\Auth;

use BoomCMS\Auth\RandomPassword;
use BoomCMS\Tests\AbstractTestCase;

class RandomPasswordTest extends AbstractTestCase
{
    /**
     * @var RandomPassword
     */
    protected $password;

    public function setUp()
    {
        parent::setUp();

        $this->password = new RandomPassword();
        $this->password->addWordList(realpath(__DIR__.'/../../vendor/genphrase/genphrase/library/GenPhrase/Wordlists/diceware.lst'), 'diceware');
    }

    public function testGetPasswordReturnsString()
    {
        $this->assertInternalType('string', $this->password->getPassword());
    }

    public function testGetPasswordAsString()
    {
        $this->assertInternalType('string', (string) $this->password);
    }
}
