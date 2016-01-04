<?php

namespace BoomCMS\Http\Controllers\Group;

use BoomCMS\Support\Facades\Group;

class Save extends BaseController
{
    public function add()
    {
        $group = Group::create(['name' => $this->request->input('name')]);

        return $group->getId();
    }

    public function addRole()
    {
        $this->group->addRole($this->request->input('role_id'), $this->request->input('allowed'), (int) $this->request->input('page_id'));
    }

    public function delete()
    {
        Group::delete($this->group);
    }

    public function removeRole()
    {
        $this->group->removeRole($this->request->input('role_id'), $this->request->input('page_id'));
    }

    public function save()
    {
        $this->group->setName($this->request->input('name'));
        Group::save($this->group);
    }
}
