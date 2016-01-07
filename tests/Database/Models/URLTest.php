<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\URL;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

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

    public function testGetPage()
    {
        $page = $this->validPage();

        $query = m::mock(Builder::class);
        $query->shouldReceive('first')->andReturn($page);

        $url = m::mock(URL::class)->makePartial();
        $url->shouldReceive('belongsTo')
            ->once()
            ->with(Page::class, 'page_id')
            ->andReturn($query);

        $this->assertEquals($page, $url->getPage());
        $this->assertEquals($page, $url->getPage());
    }

    public function testGetSite()
    {
        $this->markTestIncomplete();
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

    public function testSetSite()
    {
        $this->markTestIncomplete();
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
