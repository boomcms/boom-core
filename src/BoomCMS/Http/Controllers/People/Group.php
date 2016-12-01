<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Database\Models\Group as GroupModel;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Group as GroupFacade;
use Illuminate\Http\Request;

class Group extends Controller
{
    protected $role = 'managePeople';

    public function destroy(GroupModel $group)
    {
        GroupFacade::delete($group);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            GroupModel::ATTR_NAME => 'required',
        ]);

        return GroupFacade::create($request->input('name'));
    }

    public function update(Request $request, GroupModel $group)
    {
        $group->setName($request->input('name'));

        GroupFacade::save($group);
    }
}
