<?php

namespace BoomCMS\Tests\Http\Controllers\People;

use BoomCMS\Database\Models\Group;
use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Http\Controllers\People\Person as Controller;
use BoomCMS\Support\Facades\Group as GroupFacade;
use BoomCMS\Support\Facades\Person as PersonFacade;
use BoomCMS\Support\Facades\Site as SiteFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Mockery as m;

class PersonTest extends AbstractTestCase
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

    public function testAddSites()
    {
        $siteIds = [1, 2];
        $sites = [new Site(), new Site()];
        $request = new Request(['sites' => $siteIds]);
        $person = m::mock(Person::class);

        $person->shouldReceive('addSites')->once()->with($sites);

        SiteFacade::shouldReceive('find')->with($siteIds)->andReturn($sites);

        $this->controller->addSites($request, $person);
    }

    public function testAddSitesDoesNotQueryForSitesIfNoIdsGiven()
    {
        $request = new Request();
        $person = m::mock(Person::class);

        SiteFacade::shouldReceive('find')->never();

        $this->controller->addSites($request, $person);
    }

    public function testAddGroups()
    {
        $groupIds = [1, 2];
        $groups = [new Group(), new Group()];
        $request = new Request(['groups' => $groupIds]);
        $person = m::mock(Person::class);

        foreach ($groups as $group) {
            $person->shouldReceive('addGroup')->once()->with($group);
        }

        GroupFacade::shouldReceive('find')->with($groupIds)->andReturn($groups);

        $this->controller->addGroups($request, $person);
    }

    public function testAddGroupsDoesNotQueryForGroupsIfNoIdsGiven()
    {
        $request = new Request();
        $person = m::mock(Person::class);

        GroupFacade::shouldReceive('find')->never();

        $this->controller->addGroups($request, $person);
    }

    public function testAvailableGroups()
    {
        $groupIds = [1, 2];
        $person = m::mock(Person::class);
        $person->shouldReceive('getGroupIds')->andReturn($groupIds);

        GroupFacade::shouldReceive('allExcept')->with($groupIds);

        $this->controller->availableGroups($person);
    }

    public function testCreate()
    {
        GroupFacade::shouldReceive('findAll');

        $this->controller->create();
    }

    public function testDestroy()
    {
        $peopleIds = [1, 2];
        $request = new Request(['people' => $peopleIds]);

        PersonFacade::shouldReceive('deleteByIds')->with($peopleIds);

        $this->controller->destroy($request);
    }

    public function testRemoveGroup()
    {
        $group = new Group();
        $person = m::mock(Person::class);

        $person->shouldReceive('removeGroup')->with($group);

        $this->controller->removeGroup($person, $group);
    }

    public function testRemoveSite()
    {
        $site = new Site();
        $person = m::mock(Person::class);

        $person
            ->shouldReceive('removeSite')
            ->once()
            ->with($site);

        $this->controller->removeSite($person, $site);
    }

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
            'email' => 'support@uxblondon.com',
            'name'  => 'Test user',
        ]);

        $this->controller->shouldReceive('addGroups');
        $this->controller->store($request, $site);
    }
}
