<?php

namespace Boom\Environment;

class Development extends Environment
{
    protected $requiresLogin = true;

    public function isDevelopment()
    {
        return true;
    }
}