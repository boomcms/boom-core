<?php

use Boom\Boom;

class BoomTest extends PHPUnit_Framework_TestCase
{
    function testGetCacheDir()
    {
        $boom = Boom::instance();

        $this->assertEquals(realpath(__DIR__ . '/../../cache/'), $boom->getCacheDir(), "getCacheDir() has a default value");
    }

    function testSetCacheDir()
    {
        $boom = Boom::instance();
        $newCacheDir = __DIR__ . '/../cache';

        if ( ! file_exists($newCacheDir)) {
            mkdir($newCacheDir);
        }

        $boom->setCacheDir($newCacheDir);
        $this->assertEquals(realpath($newCacheDir), $boom->getCacheDir());

        rmdir($newCacheDir);
    }

    /**
     * @expectedException Boom\Exception
     */
    public function testSetCacheDirWithInvalidDir()
    {
        $boom = Boom::instance();

        $boom->setCacheDir('invaid');
    }
}