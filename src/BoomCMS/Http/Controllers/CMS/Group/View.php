<?php

namespace BoomCMS\Http\Controllers\CMS\Group;

use BoomCMS\Database\Models\Role;

class View extends BaseController
{
    public function add()
    {
        return view("$this->viewPrefix.add");
    }

    public function edit()
    {
        return view("$this->viewPrefix.edit", [
            'group'         => $this->group,
            'general_roles' => Role::getGeneralRoles(),
            'page_roles'    => Role::getPageRoles(),
        ]);
    }

    public function listRoles()
    {
        return $this->group->getRoles($this->request->input('page_id'));
    }
}
