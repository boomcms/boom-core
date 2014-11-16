<?php

use Boom\Boom;

class BoomTest extends PHPUnit_Framework_TestCase
{
    function testGetCacheDir()
    {
        $boom = Boom::instance();

        $this->assertEquals($boom->getCacheDir(), realpath(__DIR__ . '../cache'), "getCacheDir() has a default value");
    }
}