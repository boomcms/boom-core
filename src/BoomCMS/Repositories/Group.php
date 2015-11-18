<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Repositories\Group as GroupRepositoryInterface;
use BoomCMS\Contracts\Models\Group as GroupModelInterface;
use BoomCMS\Database\Models\Group as Model;
use Illuminate\Support\Facades\DB;

class Group implements GroupRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    public function __construct(Model $model = null)
    {
        $class = Model::class;

        $this->model = $model ?: new $class();
    }

    /**
     * @param array $groupIds
     *
     * @return array
     */
    public function allExcept(array $groupIds)
    {
        return $this->model
            ->whereNotIn(Model::ATTR_ID, $groupIds)
            ->orderBy(Model::ATTR_NAME, 'asc')
            ->get();
    }

    public function create(array $attributes)
    {
        return Model::create($attributes);
    }

    public function delete(GroupModelInterface $group)
    {
        // Delete all people roles associated with the group.
        DB::table('people_roles')
            ->where('group_id', '=', $group->getId())
            ->delete();

        $group->delete();
    }

    public function findAll()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function save(GroupModelInterface $group)
    {
        return $group->save();
    }
}
