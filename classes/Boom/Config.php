<?php

namespace Boom;

use \Kohana;

abstract class Config
{
    protected static $configGroup = 'boom';

    public static function get($key = null)
    {
        $config = Kohana::$config->load(static::$configGroup);
        return $key? $config->get($key) : $config->as_array();
    }
}
