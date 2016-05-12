<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Database\Models\Person as PersonModel;
use BoomCMS\Database\Models\Site;
use BoomCMS\Jobs\CreatePerson;
use BoomCMS\Support\Facades\Group as GroupFacade;
use BoomCMS\Support\Facades\Person as PersonFacade;
use BoomCMS\Support\Facades\Router;
use BoomCMS\Support\Facades\Site as SiteFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Gate;

class Person extends PeopleManager
{
    protected $viewPrefix = 'boomcms::person.';

    public function addSites(Request $request, PersonModel $person)
    {
        $siteIds = $request->input('sites');

        if ($siteIds) {
            $sites = SiteFacade::find($siteIds);
            $person->addSites($sites);
        }
    }

    public function destroy(Request $request)
    {
        PersonFacade::deleteByIds($request->input('people'));
    }

    public function index()
    {
        return PersonFacade::findBySite(Router::getActiveSite());
    }

    public function removeSite(PersonModel $person, Site $site)
    {
        $person->removeSite($site);
    }

    public function show(Site $site, Request $request, PersonModel $person)
    {
        return view($this->viewPrefix.'view', [
            'person'    => $person,
            'request'   => $request,
            'groups'    => GroupFacade::findBySite($site),
            'hasGroups' => $person->getGroups(),
            'sites'     => SiteFacade::findAll(),
            'hasSites'  => $person->getSites(),
        ]);
    }

    public function store(Request $request, Site $site)
    {
        $job = new CreatePerson($request->input('email'), $request->input('name'));
        $person = Bus::dispatch($job);

        foreach ($request->input('groups') as $groupId) {
            $person->addGroup(GroupFacade::find($groupId));
        }

        $person->addSite($site);

        return $person;
    }

    public function update(Request $request, PersonModel $person)
    {
        $person
            ->setName($request->input('name'))
            ->setEnabled($request->has('enabled'));

        if (Gate::allows('editSuperuser', $person)) {
            $person->setSuperuser($request->has('superuser'));
        }

        PersonFacade::save($person);
    }
}
