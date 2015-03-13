<?php

namespace BoomCMS\Core\Auth\PasswordGenerator;

class GenPhrase extends PasswordGenerator
{
    public function __construct()
    {
        $loader = new \GenPhrase\Loader('GenPhrase');
        $loader->register();
    }

    public function get_password()
    {
        $gen = new \GenPhrase\Password();

        return $gen->generate();
    }
}
