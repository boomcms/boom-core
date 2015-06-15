<?php

use BoomCMS\Core\URL\URL;
use BoomCMS\Core\Page\Page;

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

    public function testLoaded()
    {
        $url = new URL([]);
        $this->assertFalse($url->loaded());

        $url = new URL(['id' => 1]);
        $this->assertTrue($url->loaded());
    }

    public function testIsForPage()
    {
        $page = new Page(['id' => 2]);
        $url = new URL(['page_id' => 1]);
        $this->assertFalse($url->isForPage($page));

        $url = new URL(['page_id' => 2]);
        $this->assertTrue($url->isForPage($page));
    }
}