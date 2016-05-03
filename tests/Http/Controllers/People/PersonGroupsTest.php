<?php

namespace BoomCMS\Tests\Http\Controllers\People;

use BoomCMS\Database\Models\Group;
use BoomCMS\Database\Models\Person;
use BoomCMS\Http\Controllers\People\PersonGroups as Controller;
use BoomCMS\Tests\Http\Controllers\BaseControllerTest;
use Mockery as m;

class PersonGroupsTest extends BaseControllerTest
{
    /**
     * @var string
     */
    protected $className = Controller::class;

    public function store()
    {
        $group = new Group();

        $person = m::mock(Person::class);
        $person->shouldReceive('addGroup')->once()->with($group);

        $this->controller->addGroup($person, $group);
    }

    public function testDestroy()
    {
        $group = new Group();
        $person = m::mock(Person::class);

        $person->shouldReceive('removeGroup')->with($group);

        $this->controller->destroy($person, $group);
    }
}
