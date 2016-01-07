<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
use BoomCMS\Database\Models\Tag;
use BoomCMS\Http\Controllers\Page\Tags as Controller;
use BoomCMS\Support\Facades\Tag as TagFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Mockery as m;

class TagsTest extends AbstractTestCase
{
    /**
     * @var Controller
     */
    protected $controller;

    public function setUp()
    {
        parent::setUp();

        $this->controller = m::mock(Controller::class)->makePartial();
    }

    public function testAdd()
    {
        $site = new Site();
        $tag = new Tag();
        $tag->{Tag::ATTR_ID} = 1;
        $name = 'test';
        $group = 'group';
        $page = m::mock(Page::class);

        $request = new Request([
            'tag'   => $name,
            'group' => $group,
        ]);

        $page->shouldReceive('addTag')->with($tag);

        TagFacade::shouldReceive('findOrCreate')
            ->with($site, $name, $group)
            ->andReturn($tag);

        $this->assertEquals($tag->getId(), $this->controller->add($request, $site, $page));
    }

    public function testRemove()
    {
        $tag = new Tag();
        $tag->{Tag::ATTR_ID} = 1;
        $request = new Request(['tag' => $tag->getId()]);
        $page = m::mock(Page::class);

        TagFacade::shouldReceive('find')
            ->with($tag->getId())
            ->andReturn($tag);

        $page->shouldReceive('removeTag')->with($tag);

        $this->controller->remove($request, $page);
    }
}
