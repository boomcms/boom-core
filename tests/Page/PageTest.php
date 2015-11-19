<?php

namespace BoomCMS\Tests\Page;

use BoomCMS\Core\Chunk\Text;
use BoomCMS\Core\Page\Page;
use BoomCMS\Database\Models\Tag;
use BoomCMS\Support\Facades\Asset;
use BoomCMS\Support\Facades\Chunk;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\DB;

class PageTest extends AbstractTestCase
{
    public function testGetParentReturnsPageObject()
    {
        $page = new Page();

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

        $page = new Page();
        $this->assertFalse($page->hasFeatureImage());
    }

    public function testGetFeatureImageId()
    {
        $page = new Page(['feature_image_id' => 1]);
        $this->assertEquals(1, $page->getFeatureImageId());

        $page = new Page();
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
        $page = new Page();

        Chunk::shouldReceive('get')
            ->once()
            ->with('text', 'standfirst', $page)
            ->andReturn(new Text($page, ['text' => 'test standfirst', 'site_text' => 'test standfirst'], 'standfirst', false));

        $this->assertEquals('test standfirst', $page->getDescription());
    }

    public function testGetDescriptionRemovesHtml()
    {
        $page = new Page(['description' => '<p>description</p>']);
        $this->assertEquals('description', $page->getDescription());
    }

    public function testSetParentIdPageCantBeChildOfItself()
    {
        $page = new Page(['id' => 1, 'parent_id' => 2]);
        $page->setParentId($page->getId());

        $this->assertEquals(2, $page->getParentId());
    }

    public function testSetParentIdMustBeValidPage()
    {
        PageFacade::shouldReceive('findById')
            ->with(2)
            ->andReturn(new Page());

        $page = new Page();
        $page->setParentId(2);

        $this->assertNull($page->getParentId());
    }

    public function testParentIdIsSet()
    {
        PageFacade::shouldReceive('findById')
            ->with(2)
            ->andReturn(new Page(['id' => 2]));

        $page = new Page();
        $page->setParentId(2);

        $this->assertEquals(2, $page->getParentId());
    }

    public function testHasChildrenReturnsFalseIfChildCountIs0()
    {
        $page = $this->getMockBuilder('BoomCMS\Core\Page\Page')
            ->setMethods(['countChildren'])
            ->setConstructorArgs([[]])
            ->getMock();

        $page
            ->expects($this->once())
            ->method('countChildren')
            ->will($this->returnValue(0));

        $this->assertFalse($page->hasChildren());
    }

    public function testHasChildrenReturnsTrueIfChildCountGreaterThan0()
    {
        $page = $this->getMockBuilder('BoomCMS\Core\Page\Page')
            ->setMethods(['countChildren'])
            ->setConstructorArgs([[]])
            ->getMock();

        $page
           ->expects($this->once())
            ->method('countChildren')
            ->will($this->returnValue(1));

        $this->assertTrue($page->hasChildren());
    }

    public function testAddTag()
    {
        $page = $this->getMockBuilder('BoomCMS\Core\Page\Page')
            ->setMethods(['loaded'])
            ->setConstructorArgs([['id' => 1]])
            ->getMock();

        $tag = new Tag(['id' => 1]);

        $page
            ->expects($this->once())
            ->method('loaded')
            ->will($this->returnValue(true));

        DB::shouldReceive('table')
            ->once()
            ->with('pages_tags')
            ->andReturnSelf();

        DB::shouldReceive('insert')
            ->once()
            ->with([
                'page_id' => $page->getId(),
                'tag_id'  => $tag->getId(),
            ])
            ->andReturnSelf();

        $page->addTag($tag);
    }

    public function testIsParentOf()
    {
        $parent = new Page(['id' => 1]);
        $child = new Page(['parent_id' => 1]);
        $notAChild = new Page(['parent_id' => 2]);

        $this->assertTrue($parent->isParentOf($child), 'Child');
        $this->assertFalse($parent->isParentOf($notAChild), 'Not child');
    }
}
