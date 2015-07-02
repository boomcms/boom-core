<?php

namespace BoomCMS\Core\Group;

use BoomCMS\Core\Models\Group as Model;
use Illuminate\Support\Facades\DB;

class Provider
{
    public function create(array $attributes)
    {
		$m = Model::create($attributes);
		
		return new Group($m->toArray());
    }
	
	public function delete(Group $group)
	{
		// Delete all people roles associated with this group.
		DB::table('people_roles')
			->where('group_id', '=', $group->getId())
			->delete();
		
		Model::destroy($group->getId());
	}

    public function findAll()
    {
        $groups = [];

        foreach (Model::all() as $m) {
            $groups[] = new Group($m->toArray());
        }

        return $groups;
    }

    public function findById($id)
    {
		$m = Model::find($id);

        return new Group($m? $m->toArray() : []);
    }

    public function findByName($name)
    {

    }
}
