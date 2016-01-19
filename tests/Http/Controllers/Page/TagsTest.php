<?php

namespace BoomCMS\Tests\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
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
        $tag = new Tag();
        $tag->{Tag::ATTR_ID} = 1;

        $name = 'test name';
        $group = 'test group';

        $page = m::mock(Page::class);
        $page
            ->shouldReceive('addTag')
            ->once()
            ->with($tag);

        TagFacade::shouldReceive('findOrCreateByNameAndGroup')
            ->once()
            ->with($name, $group)
            ->andReturn($tag);

        $request = new Request([
            'tag'   => $name,
            'group' => $group,
        ]);

        $this->assertEquals($tag->getId(), $this->controller->add($request, $page));
    }

    public function testRemove()
    {
        $tag = new Tag();

        $page = m::mock(Page::class);
        $page
            ->shouldReceive('removeTag')
            ->once()
            ->with($tag);

        $this->controller->remove($page, $tag);
    }
}
