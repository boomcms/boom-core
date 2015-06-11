<?php

use BoomCMS\Core\Page\Page;
use BoomCMS\Core\Chunk\Text;

use BoomCMS\Core\Facades\Asset;
use BoomCMS\Core\Facades\Chunk;

class Page_PageTest extends TestCase
{
    public function testGetParentReturnsPageObject()
    {
        $page = new Page([]);

        $this->assertInstanceOf('BoomCMS\Core\Page\Page', $page->getParent());
    }

    public function testGetTemplateId()
    {
        $page = new Page(['template_id' => 1]);

        $this->assertEquals(1, $page->getTemplateId());
    }

    public function testHasFeatureImage()
    {
        $page = new Page(['feature_image_id' => 1]);
        $this->assertTrue($page->hasFeatureImage());

        $page = new Page([]);
        $this->assertFalse($page->hasFeatureImage());
    }

    public function testGetFeatureImageId()
    {
        $page = new Page(['feature_image_id' => 1]);
        $this->assertEquals(1, $page->getFeatureImageId());

        $page = new Page([]);
        $this->assertNull($page->getFeatureImageId());
    }

    public function testGetFeatureImage()
    {
        $page = new Page(['feature_image_id' => 1]);

        Asset::shouldReceive('findById')
            ->once()
            ->with($page->getFeatureImageId());

        $page->getFeatureImage();
    }

    public function testGetDescriptionReturnsDescriptionIfSet()
    {
        $page = new Page(['description' => 'test']);
        $this->assertEquals('test', $page->getDescription());
    }

    public function testGetDescriptionUsesPageStandfirstAsFallback()
    {
        $page = new Page([]);

        Chunk::shouldReceive('get')
            ->once()
            ->with('text', 'standfirst', $page)
            ->andReturn(new Text($page, ['site_text' => 'test standfirst'], 'standfirst', false));

        $this->assertEquals('test standfirst', $page->getDescription());
    }

    public function testGetDescriptionRemovesHtml()
    {
        $page = new Page(['description' => '<p>description</p>']);
        $this->assertEquals('description', $page->getDescription());
    }
}