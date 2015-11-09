<?php

namespace BoomCMS\Tests\Page;

use BoomCMS\Core\Page\Page;
use BoomCMS\Core\URL\URL;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Tests\AbstractTestCase;

class URLTest extends AbstractTestCase
{
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

    public function testGetPage()
    {
        $page = new Page(['id' => 1]);
        $url = new URL(['page_id' => 1]);

        PageFacade::shouldReceive('findById')
            ->once()
            ->andReturn($page);

        $this->assertEquals($page, $url->getPage());
    }

    public function testSetPageId()
    {
        $url = new URL(['page_id' => 1]);
        $url->setPageId(2);

        $this->assertEquals(2, $url->getPageId());
    }

    public function testSetIsPrimary()
    {
        $url = new URL(['is_primary' => true]);
        $url->setIsPrimary(false);

        $this->assertFalse($url->isPrimary());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsPrimaryWithInvalidArgument()
    {
        $url = new URL(['is_primary' => true]);
        $url->setIsPrimary('maybe');
    }

    public function testScheme()
    {
        $url = new URL(['location' => 'test']);

        $this->assertEquals('webcal://localhost/test', $url->scheme('webcal'));
    }
}
