<?php

namespace BoomCMS\Core\Controllers\CMS\People\Person;

use BoomCMS\Core\Group;
use BoomCMS\Core\Group\Provider as GroupProvider;

use Illuminate\Support\Facades\View;

class ViewPerson extends BasePerson
{
    public function add(GroupProvider $provider)
    {
        return View::make($this->viewPrefix."new", [
            'groups' => $provider->findAll(),
        ]);
    }

    public function add_group()
    {
        $finder = new Group\Finder();
        $finder
            ->addFilter(new Group\Finder\Filter\ExcludingPersonsGroups($this->editPerson))
            ->setOrderBy('name');

        return View::make("$this->viewPrefix/addgroup", [
            'person' => $this->editPerson,
            'groups' => $finder->findAll(),
        ]);
    }

    public function view()
    {
        if ( ! $this->editPerson->loaded()) {
            abourt(404);
        }

        return View::make($this->viewPrefix."view", [
            'person' => $this->editPerson,
            'request' => $this->request,
            'groups' => $this->editPerson->getGroups(),
        ]);
    }
}
