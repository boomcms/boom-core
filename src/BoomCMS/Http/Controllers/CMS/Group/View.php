<?php

namespace BoomCMS\Http\Controllers\CMS\Group;

use BoomCMS\Core\Group\Group as GroupObject;
use BoomCMS\Database\Models\Role;
use Illuminate\Support\Facades\View as ViewFacade;

class View extends BaseController
{
    public function add()
    {
        return ViewFacade::make("$this->viewPrefix/add", [
            'group' => new GroupObject([]),
        ]);
    }

    public function edit()
    {
        return ViewFacade::make("$this->viewPrefix/edit", [
            'group' => $this->group,
            'general_roles' => Role::getGeneralRoles(),
            'page_roles' => Role::getPageRoles(),
        ]);
    }

    public function listRoles()
    {
        $roles = $this->group->getRoles( (int) $this->request->input('page_id'));

        return $roles;
    }
}
