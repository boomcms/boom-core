<?php

namespace BoomCMS\Tests\Http\Controllers\People;

use BoomCMS\Database\Models\Group;
use BoomCMS\Http\Controllers\People\GroupRole as Controller;
use BoomCMS\Tests\Http\Controllers\BaseControllerTest;
use Illuminate\Http\Request;
use Mockery as m;

class GroupRoleTest extends BaseControllerTest
{
    /**
     * @var string
     */
    protected $className = Controller::class;

    public function testStore()
    {
        $roleId = 2;
        $allowed = 1;
        $pageId = 3;

        $request = new Request([
            'role_id' => $roleId,
            'allowed' => $allowed,
            'page_id' => $pageId,
        ]);

        $group = m::mock(Group::class);
        $group
            ->shouldReceive('addRole')
            ->with($roleId, $allowed, $pageId);

        $this->controller->store($request, $group);
    }

    public function testDestroy()
    {
        $roleId = 1;
        $pageId = 2;

        $request = new Request(['page_id' => $pageId]);

        $group = m::mock(Group::class);
        $group
            ->shouldReceive('removeRole')
            ->with($roleId, $pageId);

        $this->controller->destroy($request, $group, $roleId);
    }

    public function testIndex()
    {
        $roles = [1, 2, 3];
        $pageId = 1;

        $request = new Request([
            'page_id' => $pageId,
        ]);

        $group = m::mock(Group::class);
        $group
            ->shouldReceive('getRoles')
            ->with($pageId)
            ->andReturn($roles);

        $this->assertEquals($roles, $this->controller->index($request, $group));
    }
}
