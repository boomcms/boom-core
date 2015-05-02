<?php

namespace BoomCMS\Core;

class Boom
{
    /**
     *
     * @var string
     */
    private $cacheDir;

    /**
     *
     * @var Environment\Environment
     */
    private $environment;

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
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir ?: realpath(__DIR__ . '/../../cache/');
    }

    /**
     *
     * @param  string     $dir
     * @return \Boom\Boom
     */
    public function setCacheDir($dir)
    {
        $realDir = realpath($dir);

        if ($realDir === false) {
            throw new Exception("Cache directory does not exist: " . $dir);
        }

        $this->cacheDir = realpath($dir) . DIRECTORY_SEPARATOR;

        return $this;
    }
}
