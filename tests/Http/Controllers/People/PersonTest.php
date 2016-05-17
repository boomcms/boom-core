<?php

namespace BoomCMS\Tests\Http\Controllers\People;

use BoomCMS\Database\Models\Group;
use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Http\Controllers\People\Person as Controller;
use BoomCMS\Support\Facades\Group as GroupFacade;
use BoomCMS\Support\Facades\Person as PersonFacade;
use BoomCMS\Tests\Http\Controllers\BaseControllerTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Mockery as m;

class PersonTest extends BaseControllerTest
{
    /**
     * @var string
     */
    protected $className = Controller::class;

    public function testDestroy()
    {
        $person = new Person();
        PersonFacade::shouldReceive('delete')->with($person);

        $this->controller->destroy($person);
    }

    public function testStoreCanBeCalledWithoutGroups()
    {
       $person = m::mock(Person::class);
       $person->shouldReceive('addGroup')->never();

       $person->shouldReceive('addSite');

        PersonFacade::shouldReceive('create')
            ->once()
            ->andReturn($person);

        Auth::shouldReceive('user')->andReturn(new Person());
        Event::shouldReceive('fire');

        $request = new Request([
            'email'  => 'support@uxblondon.com',
            'name'   => 'Test user',
        ]);

        $this->controller->store($request, new Site());
    }

    /**
     * @depends testStoreCanBeCalledWithoutGroups
     */
    public function testStoreAddsNewPersonToCurrentSite()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;

        $person = m::mock(Person::class);
        $person
            ->shouldReceive('addSite')
            ->once()
            ->with($site);

        PersonFacade::shouldReceive('create')
            ->once()
            ->andReturn($person);

        Auth::shouldReceive('user')->andReturn(new Person());
        Event::shouldReceive('fire');

        $request = new Request([
            'email'  => 'support@uxblondon.com',
            'name'   => 'Test user',
        ]);

        $this->controller->store($request, $site);
    }

    public function testStoreAddsNewPersonToGroups()
    {
        $groupId = 1;
        $group = new Group();

        GroupFacade::shouldReceive('find')
            ->once()
            ->with($groupId)
            ->andReturn($group);
        
        $person = m::mock(Person::class);
        $person
            ->shouldReceive('addGroup')
            ->once()
            ->with($group);

        $person->shouldReceive('addSite');

        PersonFacade::shouldReceive('create')
            ->once()
            ->andReturn($person);

        Auth::shouldReceive('user')->andReturn(new Person());
        Event::shouldReceive('fire');

        $request = new Request([
            'email'  => 'support@uxblondon.com',
            'name'   => 'Test user',
            'groups' => [$groupId]
        ]);

        $this->controller->store($request, new Site());
    }

    public function testUpdatingSuperuser()
    {
        $person = m::mock(Person::class)->makePartial();

        $person
            ->shouldReceive('setSuperuser')
            ->once()
            ->with(false);

        $person
            ->shouldReceive('setSuperuser')
            ->once()
            ->with(true);

        Gate::shouldReceive('allows')
            ->times(2)
            ->with('editSuperuser', $person)
            ->andReturn(true);

        PersonFacade::shouldReceive('save')
            ->times(2)
            ->with($person);

        $enable = new Request(['superuser' => 1]);
        $disable = new Request();

        $this->controller->update($disable, $person);
        $this->controller->update($enable, $person);
    }

    public function testSuperuserIsNotChangedIfNotAllowed()
    {
        $person = m::mock(Person::class)->makePartial();
        $person
            ->shouldReceive('setSuperuser')
            ->never();

        Gate::shouldReceive('allows')
            ->once()
            ->with('editSuperuser', $person)
            ->andReturn(false);

        PersonFacade::shouldReceive('save')
            ->once()
            ->with($person);

        $this->controller->update(new Request(), $person);
    }
}
