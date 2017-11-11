<?php

namespace BoomCMS\Tests\Http\Controllers\People;

use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Http\Controllers\People\Person as Controller;
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

    public function testStoreAddsNewPersonToCurrentSite()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;

        $email = 'support@uxblondon.com';

        $person = m::mock(Person::class);
        $person
            ->shouldReceive('addSite')
            ->once()
            ->with($site);

        $person
            ->shouldReceive('hasSite')
            ->once()
            ->with($site)
            ->andReturn(false);

        PersonFacade::shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($person);

        Auth::shouldReceive('user')->andReturn(new Person());
        Event::shouldReceive('fire');

        $request = new Request([
            'email' => $email,
            'name'  => 'Test user',
        ]);

        $this->controller->store($request, $site);
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
