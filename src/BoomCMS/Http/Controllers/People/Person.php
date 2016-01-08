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
        foreach ($request->input('groups') as $groupId) {
            $group = GroupFacade::find($groupId);

            if ($group) {
                $person->addGroup($group);
            }
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
