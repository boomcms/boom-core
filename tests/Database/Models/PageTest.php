<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Core\Chunk\Text;
use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Tag;
use BoomCMS\Support\Facades\Asset;
use BoomCMS\Support\Facades\Chunk;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\DB;

class PageTest extends AbstractTestCase
{
    public function testGetChildOrderingPolicy()
    {
        $values = [
            Page::ORDER_TITLE => ['title', 'desc'], // Default is descending
            Page::ORDER_TITLE | Page::ORDER_ASC => ['title', 'asc'],
            Page::ORDER_TITLE | Page::ORDER_DESC => ['title', 'desc'],
            Page::ORDER_VISIBLE_FROM | Page::ORDER_ASC => ['visible_from', 'asc'],
            Page::ORDER_VISIBLE_FROM | Page::ORDER_DESC => ['visible_from', 'desc'],
            Page::ORDER_SEQUENCE | Page::ORDER_ASC => ['sequence', 'asc'],
            Page::ORDER_SEQUENCE | Page::ORDER_DESC => ['sequence', 'desc'],
            0 => ['sequence', 'desc'],
        ];

        foreach ($values as $order => $expected) {
            $page = new Page([Page::ATTR_CHILD_ORDERING_POLICY => $order]);

            $this->assertEquals($expected, $page->getChildOrderingPolicy());
        }
    }

    public function testGetParentReturnsPageObject()
    {
        $page = new Page();

        $this->assertInstanceOf(Page::class, $page->getParent());
    }

    public function testGetTemplateId()
    {
        $page = new Page(['template_id' => 1]);

        $this->assertEquals(1, $page->getTemplateId());
    }

    public function testHasFeatureImage()
    {
        $page = new Page([Page::ATTR_FEATURE_IMAGE => 1]);
        $this->assertTrue($page->hasFeatureImage());

        $page = new Page();
        $this->assertFalse($page->hasFeatureImage());
    }

    public function testGetFeatureImageId()
    {
        $page = new Page([Page::ATTR_FEATURE_IMAGE => 1]);
        $this->assertEquals(1, $page->getFeatureImageId());

        $page = new Page();
        $this->assertNull($page->getFeatureImageId());
    }

    public function testGetFeatureImage()
    {
        $page = new Page([Page::ATTR_FEATURE_IMAGE => 1]);

        Asset::shouldReceive('findById')
            ->once()
            ->with($page->getFeatureImageId());

        $page->getFeatureImage();
    }

    public function testGetDescriptionReturnsDescriptionIfSet()
    {
        $page = new Page([Page::ATTR_DESCRIPTION => 'test']);
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
        $page = new Page([Page::ATTR_DESCRIPTION => '<p>description</p>']);
        $this->assertEquals('description', $page->getDescription());
    }

    public function testSetAndGetChildOrderingPolicy()
    {
        $values = [
            ['title', 'asc'],
            ['title', 'desc'],
            ['visible_from', 'asc'],
            ['visible_from', 'desc'],
            ['sequence', 'asc'],
            ['sequence', 'desc'],
        ];

        foreach ($values as $v) {
            list($column, $direction) = $v;

            $page = new Page();
            $page->setChildOrderingPolicy($column, $direction);
            list($newCol, $newDirection) = $page->getChildOrderingPolicy();

            $this->assertEquals($column, $newCol);
            $this->assertEquals($direction, $newDirection);
        }
    }

    public function testSetParentPageCantBeChildOfItself()
    {
        $page = new Page([Page::ATTR_ID => 1, Page::ATTR_PARENT => 2]);
        $page->setParent($page);

        $this->assertEquals(2, $page->getParentId());
    }

    public function testHasChildrenReturnsFalseIfChildCountIs0()
    {
        $page = $this->getMockBuilder(Page::class)
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
        $page = $this->getMockBuilder(Page::class)
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
        $page = $this->getMockBuilder(Page::class)
            ->setMethods(['loaded'])
            ->setConstructorArgs([[Page::ATTR_ID => 1]])
            ->getMock();

        $tag = new Tag([Tag::ATTR_ID => 1]);

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
        $parent = new Page([Page::ATTR_ID => 1]);
        $child = new Page([Page::ATTR_PARENT => 1]);
        $notAChild = new Page([Page::ATTR_PARENT => 2]);

        $this->assertTrue($parent->isParentOf($child), 'Child');
        $this->assertFalse($parent->isParentOf($notAChild), 'Not child');
    }

    public function testSetSequence()
    {
        $page = new Page();

        $this->assertEquals($page, $page->setSequence(2));
        $this->assertEquals(2, $page->getManualOrderPosition());
    }
}
