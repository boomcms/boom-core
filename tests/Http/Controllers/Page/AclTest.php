<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Group;
use BoomCMS\Database\Models\Page;
use BoomCMS\Http\Controllers\Page\Acl as Controller;
use BoomCMS\Support\Facades\Page as PageFacade;
use Illuminate\Http\Request;
use Mockery as m;

class AclTest extends BaseControllerTest
{
    protected $className = Controller::class;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var Group
     */
    protected $group;

    public function setUp()
    {
        parent::setUp();

        $this->page = m::mock(Page::class)->makePartial();
        $this->page->{Page::ATTR_ID} = 1;

        $this->group = new Group();
        $this->group->{Group::ATTR_ID} = 1;

        PageFacade::shouldReceive('findByParentId')
            ->with($this->page->getId())
            ->andReturn(null);
    }

    public function testDestroy()
    {
        $this->requireRole('editAcl', $this->page);

        $this->page
            ->shouldReceive('removeAclGroupId')
            ->once()
            ->with($this->group->getId());

        PageFacade::shouldReceive('recurse')
            ->once()
            ->with($this->page, m::on(function($closure) {
                $closure($this->page);

                return true;
            }));

        $this->controller->destroy($this->page, $this->group);
    }

    public function testStore()
    {
        $this->requireRole('editAcl', $this->page);

        $this->page
            ->shouldReceive('addAclGroupId')
            ->once()
            ->with($this->group->getId());

        PageFacade::shouldReceive('recurse')
            ->once()
            ->with($this->page, m::on(function($closure) {
                $closure($this->page);

                return true;
            }));

        $this->controller->store($this->page, $this->group);
    }
}
