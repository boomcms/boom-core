<?php

namespace BoomCMS\Http\Controllers\CMS\Group;

use BoomCMS\Database\Models\Role;
use Illuminate\Support\Facades\View as ViewFacade;

class View extends BaseController
{
    public function add()
    {
        return ViewFacade::make("$this->viewPrefix/add");
    }

    public function edit()
    {
        return ViewFacade::make("$this->viewPrefix/edit", [
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
