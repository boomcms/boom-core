<?php

namespace Boom\Environment;

abstract class Environment
{
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
}