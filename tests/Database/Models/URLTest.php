<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\URL;

class URLTest extends AbstractModelTestCase
{
    protected $model = URL::class;

    public function testIsPrimary()
    {
        $url = new URL();
        $this->assertFalse($url->isPrimary());

        $url = new URL(['is_primary' => true]);
        $this->assertTrue($url->isPrimary());
    }

    public function testGetLocation()
    {
        $url = new URL();
        $this->assertNull($url->getLocation());

        $url = new URL(['location' => 'test/test']);
        $this->assertEquals('test/test', $url->getLocation());
    }

    public function testIsForPage()
    {
        $page = new Page();
        $page->id = 2;

        $url = new URL(['page_id' => 1]);
        $this->assertFalse($url->isForPage($page));

        $url = new URL(['page_id' => 2]);
        $this->assertTrue($url->isForPage($page));
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
