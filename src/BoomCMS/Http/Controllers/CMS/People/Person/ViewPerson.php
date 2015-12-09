<?php

namespace BoomCMS\Http\Controllers\CMS\People\Person;

use BoomCMS\Support\Facades\Group as GroupFacade;
use Illuminate\Support\Facades\View;

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
        if (!$this->editPerson) {
            abourt(404);
        }

        return view($this->viewPrefix.'view', [
            'person'  => $this->editPerson,
            'request' => $this->request,
            'groups'  => $this->editPerson->getGroups(),
        ]);
    }
}
