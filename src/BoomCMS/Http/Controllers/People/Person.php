<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Database\Models\Group as GroupModel;
use BoomCMS\Database\Models\Person as PersonModel;
use BoomCMS\Jobs\CreatePerson;
use BoomCMS\Support\Facades\Group as GroupFacade;
use BoomCMS\Support\Facades\Person as PersonFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Gate;

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

    public function removeGroup(PersonModel $person, GroupModel $group)
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
        $groups = $request->input('groups') ?: [];

        Bus::dispatch(new CreatePerson(
            [
                'email' => $request->input('email'),
                'name'  => $request->input('name'),
            ],
            $groups
        ));
    }

    public function update(Request $request, PersonModel $person)
    {
        $person
            ->setName($request->input('name'))
            ->setEnabled($request->has('enabled'));

        if ($request->input('superuser') && Gate::allows('editSuperuser', $person)) {
            $person->setSuperuser($request->has('superuser'));
        }

        PersonFacade::save($person);
    }
}
