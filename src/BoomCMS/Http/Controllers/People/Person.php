<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Database\Models\Person as PersonModel;
use BoomCMS\Database\Models\Site;
use BoomCMS\Jobs\CreatePerson;
use BoomCMS\Support\Facades\Person as PersonFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Gate;

class Person extends PeopleManager
{
    protected $viewPrefix = 'boomcms::person.';

    public function destroy(PersonModel $person)
    {
        PersonFacade::delete($person);
    }

    public function index(Site $site)
    {
        return PersonFacade::findBySite($site);
    }

    public function store(Request $request, Site $site)
    {
        $job = new CreatePerson($request->input('email'), $request->input('name'));

        $person = Bus::dispatch($job);
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
