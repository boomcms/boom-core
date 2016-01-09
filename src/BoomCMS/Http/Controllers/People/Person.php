<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Database\Models\Group;
use BoomCMS\Database\Models\Person as PersonModel;
use BoomCMS\Jobs\CreatePerson;
use BoomCMS\Support\Facades\Group as GroupFacade;
use BoomCMS\Support\Facades\Person as PersonFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;

class Person extends PeopleManager
{
    protected $viewPrefix = 'boomcms::person.';
    protected $role = 'managePeople';

    public function addGroups(Request $request, PersonModel $person)
    {
        $groups = GroupFacade::find($request->input('groups'));

        foreach ($groups as $group) {
            $person->addGroup($group);
        }
    }

    public function availableGroups(PersonModel $person)
    {
        return view("$this->viewPrefix.addgroup", [
            'person' => $person,
            'groups' => GroupFacade::allExcept($person->getGroupIds()),
        ]);
    }

    public function create()
    {
        return view("$this->viewPrefix.new", [
            'groups' => GroupFacade::findAll(),
        ]);
    }

    public function destroy(Request $request)
    {
        PersonFacade::deleteByIds($request->input('people'));
    }

    public function removeGroup(PersonModel $person, Group $group)
    {
        $person->removeGroup($group);
    }

    public function show(Request $request, PersonModel $person)
    {
        return view($this->viewPrefix.'view', [
            'person'  => $person,
            'request' => $request,
            'groups'  => $person->getGroups(),
        ]);
    }

    public function store(Request $request)
    {
        $job = new CreatePerson($request->input('email'), $request->input('name'));
        $person = Bus::dispatch($job);

        $this->addGroups($request, $person);
    }

    public function update(Request $request, PersonModel $person)
    {
        $superuser = $request->input('superuser');

        $person
            ->setName($request->input('name'))
            ->setEnabled($request->input('enabled') == 1);

        if ($superuser !== null && Auth::check('editSuperuser', $person)) {
            $person->setSuperuser($superuser == 1);
        }

        PersonFacade::save($person);
    }
}
