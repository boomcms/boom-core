<?php

namespace BoomCMS\Tests\Http\Controllers\People;

use BoomCMS\Database\Models\Group;
use BoomCMS\Database\Models\Site;
use BoomCMS\Http\Controllers\People\Group as Controller;
use BoomCMS\Support\Facades\Group as GroupFacade;
use BoomCMS\Tests\Http\Controllers\BaseControllerTest;
use Illuminate\Http\Request;
use Mockery as m;

class GroupTest extends BaseControllerTest
{
    /**
     * @var string
     */
    protected $className = Controller::class;

    public function testDestroy()
    {
        $group = new Group();
        GroupFacade::shouldReceive('delete')->with($group);

        $this->controller->destroy($group);
    }

    /**
     * @expectedException Illuminate\Validation\ValidationException
     */
    public function testStoreValidatesNameIsRequired()
    {
        $request = new Request();

        $this->controller->store($request, new Site());
    }

    public function testStore()
    {
        $name = 'test';
        $request = new Request([
            'name' => $name,
        ]);

        $group = new Group();
        $group->{Group::ATTR_ID} = 1;

        $site = new Site();
        $site->{Site::ATTR_ID} = 1;

        GroupFacade::shouldReceive('create')
            ->with($site, $name)
            ->andReturn($group);

        $this->assertEquals($group, $this->controller->store($request, $site));
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
