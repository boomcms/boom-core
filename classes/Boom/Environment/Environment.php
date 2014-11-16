<?php

namespace Boom\Environment;

use Exception;
use Boom\Exception\Handler\Priv as PrivateExceptionHandler;

abstract class Environment
{
    /**
     *
     * @var boolean
     */
    protected $requiresLogin = false;

    /**
     *
     * @param Exception $e
     * @return PrivateExceptionHandler
     */
    public function getExceptionHandler(Exception $e)
    {
        return new PrivateExceptionHandler($e);
    }

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