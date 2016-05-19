<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Database\Models\Group as GroupModel;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupRole extends Controller
{
    protected $role = 'managePeople';

    public function destroy(Request $request, GroupModel $group, $roleId)
    {
        $group->removeRole($roleId, $request->input('page_id'));
    }

    public function index(Request $request, GroupModel $group)
    {
        return $group->getRoles($request->input('page_id'));
    }

    public function store(Request $request, GroupModel $group)
    {
        $group->addRole($request->input('role_id'), $request->input('allowed'), $request->input('page_id'));
    }
}
