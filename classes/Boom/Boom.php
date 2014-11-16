<?php

namespace Boom;

class Boom
{
    private $cacheDir;

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

    /**
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir?: realpath(__DIR__ . '/cache');
    }
}