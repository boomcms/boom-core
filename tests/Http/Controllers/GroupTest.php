<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Group;
use BoomCMS\Http\Controllers\Group as Controller;
use BoomCMS\Support\Facades\Group as GroupFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Mockery as m;

class GroupTest extends AbstractTestCase
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

    public function testAddRole()
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

        $this->controller->addRole($request, $group);
    }

    public function testDestroy()
    {
        $group = new Group();
        GroupFacade::shouldReceive('delete')->with($group);

        $this->controller->destroy($group);
    }

    public function testRemoveRole()
    {
        $roleId = 1;
        $pageId = 2;

        $request = new Request([
            'role_id' => $roleId,
            'page_id' => $pageId,
        ]);

        $group = m::mock(Group::class);
        $group
            ->shouldReceive('removeRole')
            ->with($roleId, $pageId);

        $this->controller->removeRole($request, $group);
    }

    public function testRoles()
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

        $this->assertEquals($roles, $this->controller->roles($request, $group));
    }

    public function testStore()
    {
        $name = 'test';
        $request = new Request([
            'name' => $name,
        ]);

        $group = new Group();
        $group->id = 1;

        GroupFacade::shouldReceive('create')
            ->with(['name' => $name])
            ->andReturn($group);

        $this->assertEquals($group->getId(), $this->controller->store($request));
    }

    public function testUpdate()
    {
        $name = 'test';
        $request = new Request([
            'name' => $name,
        ]);

        $group = m::mock(Group::class);
        $group
            ->shouldReceive('setName')
            ->with($name);

        GroupFacade::shouldReceive('save')->with($group);

        $this->controller->update($request, $group);
    }
}
