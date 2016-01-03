<?php

namespace BoomCMS\Auth;

class RandomPassword
{
    protected $password;

    public function __construct()
    {
        $loader = new \GenPhrase\Loader('GenPhrase');
        $loader->register();

        $gen = new \GenPhrase\Password();
        $this->password = $gen->generate();
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function __toString()
    {
        return $this->getPassword();
    }
}
