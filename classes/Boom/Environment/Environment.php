<?php

namespace Boom\Environment;

abstract class Environment
{
    /**
     *
     * @var boolean
     */
    protected $requiresLogin = false;

    public function isDevelopment()
    {
        return false;
    }

    public function isProduction()
    {
        return false;
    }

    public function isStaging()
    {
        return false;
    }

    public function requiresLogin()
    {
        return $this->requiresLogin;
    }
}