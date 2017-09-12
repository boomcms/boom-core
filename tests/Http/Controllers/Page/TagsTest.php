<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Tag;
use BoomCMS\Http\Controllers\Page\Tags as Controller;
use BoomCMS\Support\Facades\Tag as TagFacade;
use Illuminate\Http\Request;
use Mockery as m;

class TagsTest extends BaseControllerTest
{
    /**
     * @var string
     */
    protected $className = Controller::class;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var Tag
     */
    protected $tag;

    public function setUp()
    {
        parent::setUp();

        $this->tag = new Tag();
        $this->tag->{Tag::ATTR_ID} = 1;

        $this->page = m::mock(Page::class);

        $this->requireRole('edit', $this->page);
    }

    public function testAdd()
    {
        $name = 'test';
        $group = 'group';

        $request = new Request([
            'tag'   => $name,
            'group' => $group,
        ]);

        $this->page->shouldReceive('addTag')->with($this->tag);

        TagFacade::shouldReceive('findOrCreate')
            ->with($name, $group)
            ->andReturn($this->tag);

        $this->assertEquals($this->tag->getId(), $this->controller->add($request, $this->page));
    }

    public function testRemove()
    {
        $this->page
            ->shouldReceive('removeTag')
            ->once()
            ->with($this->tag)
            ->andReturnSelf();

        $this->controller->remove($this->page, $this->tag);
    }
}
