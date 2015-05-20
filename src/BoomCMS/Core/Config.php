<?php

namespace BoomCMS\Core;

use Illuminate\Support\Arr;

abstract class Config
{
    protected static $config;

    public static function get($key = null)
    {
        if (static::$config === null) {
            static::$config = static::load();
        }

        return Arr::get(static::$config, $key);
    }

    protected static function load()
    {
        return include __DIR__ . '/../../config/boom.php';
    }
}
