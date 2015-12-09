<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Tag as TagInterface;
use BoomCMS\Database\Models\Tag as Model;

class Tag
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $name
     * @param string $group
     *
     * @return TagInterface
     */
    public function create($name, $group)
    {
        return $this->model->create([
            Model::ATTR_NAME  => $name,
            Model::ATTR_GROUP => $group,
        ]);
    }

    /**
     * @param int $id
     *
     * @return TagInterface
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * @param string $name
     *
     * @return TagInterface
     */
    public function findByName($name)
    {
        return $this->model->where(Model::ATTR_NAME, '=', $name)->first();
    }

    /**
     * @param string $name
     * @param string $group
     *
     * @return TagInterface
     */
    public function findByNameAndGroup($name, $group = null)
    {
        return $this->model
            ->where(Model::ATTR_NAME, '=', $name)
            ->where(Model::ATTR_GROUP, '=', $group)
            ->first();
    }

    /**
     * @param string $slug
     * @param string $group
     *
     * @return TagInterface
     */
    public function findBySlugAndGroup($slug, $group = null)
    {
        return $this->model
            ->where(Model::ATTR_NAME, '=', $slug)
            ->where(Model::ATTR_GROUP, '=', $group)
            ->first();
    }

    /**
     * @param string $name
     * @param string $group
     *
     * @return TagInterface
     */
    public function findOrCreateByNameAndGroup($name, $group = null)
    {
        // Ensure group is null if an empty string is passed.
        $group = $group ?: null;
        $tag = $this->findByNameAndGroup($name, $group);

        return $tag ?: $this->create($name, $group);
    }
}
