<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
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

        $page = m::mock(Page::class);
        $page->shouldReceive('removeTag')->with($tag);

        $this->controller->remove($page, $tag);
    }
}
