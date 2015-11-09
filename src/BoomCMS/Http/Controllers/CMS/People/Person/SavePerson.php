<?php

namespace BoomCMS\Http\Controllers\CMS\People\Person;

use BoomCMS\Jobs\CreatePerson;
use BoomCMS\Support\Facades\Group;
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
            $group = Group::findById($groupId);

            if ($group->loaded()) {
                $this->editPerson->addGroup($group);
            }
        }
    }

    public function delete()
    {
        $this->personProvider->deleteByIds($this->request->input('people'));
    }

    public function removeGroup()
    {
        $group = Group::findById($this->request->input('group_id'));
        $this->editPerson->removeGroup($group);
    }

    public function save()
    {
        $this->editPerson
            ->setName($this->request->input('name'))
            ->setEnabled($this->request->input('enabled') == 1);

        if ($superuser = $this->request->input('superuser')
            && $this->auth->getPerson()->isSuperuser()
            && $this->auth->getPerson()->getId() != $this->editPerson->getId()
        ) {
            $this->editPerson->setSuperuser($superuser == 1);
        }

        $this->personProvider->save($this->editPerson);
    }
}
