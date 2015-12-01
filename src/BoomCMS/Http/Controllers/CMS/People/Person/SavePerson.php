<?php

namespace BoomCMS\Http\Controllers\CMS\People\Person;

use BoomCMS\Jobs\CreatePerson;
use BoomCMS\Support\Facades\Auth;
use BoomCMS\Support\Facades\Group;
use BoomCMS\Support\Facades\Person;
use Illuminate\Support\Facades\Bus;

class SavePerson extends BasePerson
{
    public function add()
    {
        Bus::dispatch(new CreatePerson(
            [
                'email' => $this->request->input('email'),
                'name'  => $this->request->input('name'),
            ],
            $this->request->input('groups') ?: []
        ));
    }

    public function addGroup()
    {
        foreach ($this->request->input('groups') as $groupId) {
            $group = Group::find($groupId);

            if ($group) {
                $this->editPerson->addGroup($group);
            }
        }
    }

    public function delete()
    {
        Person::deleteByIds($this->request->input('people'));
    }

    public function removeGroup()
    {
        $group = Group::find($this->request->input('group_id'));
        $this->editPerson->removeGroup($group);
    }

    public function save()
    {
        $superuser = $this->request->input('superuser');

        $this->editPerson
            ->setName($this->request->input('name'))
            ->setEnabled($this->request->input('enabled') == 1);

        if ($superuser !== null
            && Auth::getPerson()->isSuperuser()
            && Auth::getPerson()->getId() != $this->editPerson->getId()
        ) {
            $this->editPerson->setSuperuser($superuser == 1);
        }

        Person::save($this->editPerson);
    }
}
