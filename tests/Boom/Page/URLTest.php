<?php

use BoomCMS\Core\URL\URL;

class Page_URLTest extends TestCase
{
    public function testGetId()
    {
        $url = new URL([]);
        $this->assertNull($url->getId());

        $url = new URL(['id' => 1]);
        $this->assertEquals(1, $url->getId());
    }

    public function testIsPrimary()
    {
        $url = new URL([]);
        $this->assertFalse($url->isPrimary());

        $url = new URL(['is_primary' => true]);
        $this->assertTrue($url->isPrimary());
    }

    public function testGetLocation()
    {
        $url = new URL([]);
        $this->assertNull($url->getLocation());

        $url = new URL(['location' => 'test/test']);
        $this->assertEquals('test/test', $url->getLocation());
    }
}