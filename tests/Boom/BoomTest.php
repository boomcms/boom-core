<?php

use Boom\Boom;

class BoomTest extends PHPUnit_Framework_TestCase
{
    private $boom;

    public function setUp()
    {
        parent::setUp();

        $this->boom = Boom::instance();
    }

    function testGetCacheDir()
    {
        $this->assertEquals(realpath(__DIR__ . '/../../cache/'), $this->boom->getCacheDir(), "getCacheDir() has a default value");
    }

    function testSetCacheDir()
    {
        $newCacheDir = __DIR__ . '/../cache';

        if ( ! file_exists($newCacheDir)) {
            mkdir($newCacheDir);
        }

        $this->boom->setCacheDir($newCacheDir);
        $this->assertEquals(realpath($newCacheDir), $this->boom->getCacheDir());

        rmdir($newCacheDir);
    }

    /**
     * @expectedException Boom\Exception
     */
    public function testSetCacheDirWithInvalidDir()
    {
        $this->boom->setCacheDir('invaid');
    }

    public function testGetEnvironment()
    {
        $this->assertInstanceOf('Boom\Environment\Production', $this->boom->getEnvironment());
        $this->assertInstanceOf('Boom\Environment\Development', $this->boom->setEnvironment('development')->getEnvironment());
        $this->assertInstanceOf('Boom\Environment\Staging', $this->boom->setEnvironment('staging')->getEnvironment());
    }

    /**
     * @expectedException Boom\Exception
     */
    public function testSetEnvironmentWithInvalidArgument()
    {
        $this->boom->setEnvironment('invalid');
    }
}