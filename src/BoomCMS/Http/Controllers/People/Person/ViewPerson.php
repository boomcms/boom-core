<?php

namespace BoomCMS\Http\Controllers\People\Person;

use BoomCMS\Support\Facades\Group as GroupFacade;

class ViewPerson extends BasePerson
{
    public function add()
    {
        return view($this->viewPrefix.'new', [
            'groups' => GroupFacade::findAll(),
        ]);
    }

    public function addGroup()
    {
        return view("$this->viewPrefix/addgroup", [
            'person' => $this->editPerson,
            'groups' => GroupFacade::allExcept($this->editPerson->getGroupIds()),
        ]);
    }

    public function view()
    {
        return view($this->viewPrefix.'view', [
            'person'  => $this->editPerson,
            'request' => $this->request,
            'groups'  => $this->editPerson->getGroups(),
        ]);
    }
}
