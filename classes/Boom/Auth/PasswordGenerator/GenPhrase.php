<?php

namespace Boom\Auth\PasswordGenerator;

use \Kohana as Kohana;

class GenPhrase extends PasswordGenerator
{
    public function __construct()
    {
        require Kohana::find_file('vendor', 'genphrase/library/GenPhrase/Loader');
        $loader = new \GenPhrase\Loader('GenPhrase');
        $loader->register();
    }

    public function get_password()
    {
        $gen = new \GenPhrase\Password();

        return $gen->generate();
    }
}
