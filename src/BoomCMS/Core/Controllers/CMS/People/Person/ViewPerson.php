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
            ->addFilter(new Group\Finder\Filter\ExcludingPersonsGroups($this->edit_person))
            ->setOrderBy('name');

        return View::make("$this->viewPrefix/addgroup", [
            'person' => $this->edit_person,
            'groups' => $finder->findAll(),
        ]);
    }

    public function view()
    {
        if ( ! $this->edit_person->loaded()) {
            abourt(404);
        }

        return View::make($this->viewPrefix."view", [
            'person' => $this->edit_person,
            'request' => $this->request,
            'groups' => $this->edit_person->getGroups(),
            'activities' => [],
        ]);
    }
}
