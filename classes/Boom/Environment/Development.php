<?php

namespace Boom\Environment;

use Exception;
use Boom\Exception\Handler\Pub as PublicExceptionHandler;

class Development extends Environment
{
    protected $requiresLogin = true;

    /**
     *
     * @param Exception $e
     * @return PublicExceptionHandler
     */
    public function getExceptionHandler(Exception $e)
    {
        return new PublicExceptionHandler($e);
    }

    public function isDevelopment()
    {
        return true;
    }
}