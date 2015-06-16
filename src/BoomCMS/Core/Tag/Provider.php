<?php

namespace BoomCMS\Core\Tag;

use BoomCMS\Core\Models\Tag as Model;

class Provider
{
    public function byId($id)
    {
		$model = Model::find($id);

        return $model ? new Tag($model->toArray()) : new Tag();
    }

    public function byName($name)
    {
		$model = Model::where('name', $name)->first();

        return $model ? new Tag($model->toArray()) : new Tag();
    }
	
	public function create($name, $group)
	{
		$m = Model::create([
			'name' => $name,
			'group' => $group
		]);
		
		return new Tag($m->toArray());
	}

    public function findByNameAndGroup($name, $group= null)
    {
		$model = Model::where('name', $name)
			->where('group', $group)
			->first();

        return $model ? new Tag($model->toArray()) : new Tag();
    }

    public function findOrCreateByNameAndGroup($name, $group = null)
    {
        // Ensure group is null if an empty string is passed.
        $group = $group ?: null;

        if ( ! $this->findByNameAndGroup($name, $group)->loaded()) {
			return $this->create($name, $group);
        }
    }
}
