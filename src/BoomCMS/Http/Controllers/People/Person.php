<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Database\Models\Person as PersonModel;
use BoomCMS\Database\Models\Site;
use BoomCMS\Jobs\CreatePerson;
use BoomCMS\Support\Facades\Person as PersonFacade;
use Illuminate\Http\Request;
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
        $person = PersonFacade::findByEmail($request->input('email'));

        if ($person !== null) {
            if (!$person->hasSite($site)) {
                $person->addSite($site);
            }

            return $person;
        }

        $job = new CreatePerson($request->input('email'), $request->input('name'));
        $person = $this->dispatch($job);

        return $person->addSite($site);
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
