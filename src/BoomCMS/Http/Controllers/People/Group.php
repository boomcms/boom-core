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

    public function store(Request $request, Site $site)
    {
        return GroupFacade::create($site, $request->input('name'));
    }

    public function update(Request $request, GroupModel $group)
    {
        $group->setName($request->input('name'));

        GroupFacade::save($group);
    }
}
