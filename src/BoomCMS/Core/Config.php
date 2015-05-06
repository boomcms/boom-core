<?php

namespace BoomCMS\Core;

abstract class Config
{
    protected static $config;

    public static function get($key = null)
    {
        if (static::$config === null) {
            static::$config = static::load();
        }

        return isset(static::$config[$key])? static::$config[$key] : null;
    }

    protected static function load()
    {
        return include __DIR__ . '/../../config/boom.php';
    }
}
