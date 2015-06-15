<?php

namespace BoomCMS\Core\Environment;

use Exception;
use BoomCMS\Core\Exception\Handler\Priv as PrivateExceptionHandler;

abstract class Environment
{
    /**
     *
     * @param  Exception               $e
     * @return PrivateExceptionHandler
     */
    public function getExceptionHandler(Exception $e)
    {
        return new PrivateExceptionHandler($e);
    }

    /**
     *
     * @return boolean
     */
    public function isDevelopment()
    {
        return false;
    }

    /**
     *
     * @return boolean
     */
    public function isProduction()
    {
        return false;
    }

    /**
     *
     * @return boolean
     */
    public function isStaging()
    {
        return false;
    }
}
