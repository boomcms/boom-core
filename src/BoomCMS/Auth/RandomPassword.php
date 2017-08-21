<?php

namespace BoomCMS\Auth;

use GenPhrase\Loader;
use GenPhrase\Password;

/**
 * Wrapper around GenPhrase password generator.
 *
 * Uses the EFF Diceware list to generate a six word password
 *
 * @see https://github.com/timoh6/GenPhrase
 * @see https://www.eff.org/deeplinks/2016/07/new-wordlists-random-passphrases
 */
class RandomPassword
{
    /**
     * @var type Password
     */
    protected $generator;

    protected $password;

    public function __construct()
    {
        $loader = new Loader('GenPhrase');
        $loader->register();

        $this->generator = new Password();
        $this->generator->removeWordlist('default');
        $this->generator->disableSeparators(true);
        $this->generator->disableWordModifier(true);

        $this->addWordList(base_path('vendor/genphrase/genphrase/library/GenPhrase/Wordlists/diceware.lst'), 'diceware');
    }

    /**
     * Add a word list to the generator.
     *
     * @param string $path
     * @param string $name
     *
     * @return $this
     */
    public function addWordList(string $path, $name): self
    {
        $this->generator->addWordList($path, $name);

        return $this;
    }

    /**
     * Returns a password with 65 bits of entropy (6 words).
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->generator->generate(65);
    }

    public function __toString(): string
    {
        return $this->getPassword();
    }
}
