<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Database\Models\Group as GroupModel;
use BoomCMS\Database\Models\Role;
use BoomCMS\Database\Models\Site;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Group as GroupFacade;
use Illuminate\Http\Request;

class Group extends Controller
{
    protected $viewPrefix = 'boomcms::groups.';
    protected $role = 'managePeople';

    public function addRole(Request $request, GroupModel $group)
    {
        $group->addRole($request->input('role_id'), $request->input('allowed'), $request->input('page_id'));
    }

    public function create()
    {
        return view("$this->viewPrefix.add");
    }

    public function destroy(GroupModel $group)
    {
        GroupFacade::delete($group);
    }

    public function show(GroupModel $group)
    {
        return view("$this->viewPrefix.edit", [
            'group'         => $group,
            'general_roles' => Role::getGeneralRoles(),
            'page_roles'    => Role::getPageRoles(),
        ]);
    }

    /**
     * @param Site $site
     *
     * @return array
     */
    public function index(Site $site)
    {
        return GroupFacade::findBySite($site);
    }

    public function removeRole(Request $request, GroupModel $group)
    {
        $group->removeRole($request->input('role_id'), $request->input('page_id'));
    }

    public function roles(Request $request, GroupModel $group)
    {
        return $group->getRoles($request->input('page_id'));
    }

    public function store(Request $request, Site $site)
    {
        $group = GroupFacade::create($site, $request->input('name'));

        return $group->getId();
    }

    public function update(Request $request, GroupModel $group)
    {
        $group->setName($request->input('name'));

        GroupFacade::save($group);
    }
}
