<?php

namespace Boom;

class Boom
{
    /**
     *
     * @var Boom
     */
    private static $instance;

    private function __construct()
    {

    }

    public static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static;
        }

        return static::$instance;
    }
}