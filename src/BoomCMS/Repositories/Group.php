<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Group as GroupModelInterface;
use BoomCMS\Contracts\Models\Site as SiteModelInterface;
use BoomCMS\Contracts\Repositories\Group as GroupRepositoryInterface;
use BoomCMS\Database\Models\Group as Model;
use Illuminate\Database\Eloquent\Collection;

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
     * @param SiteModelInterface $site
     * @param string             $name
     *
     * @return Model
     */
    public function create($name)
    {
        return $this->model->create([
            Model::ATTR_NAME => $name,
        ]);
    }

    public function delete(GroupModelInterface $group)
    {
        $group->delete();

        return $this;
    }

    public function findAll()
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * Find a group by its ID.
     *
     * @param int $groupId
     *
     * @return GroupModelInterface
     */
    public function find($groupId)
    {
        return $this->model->find($groupId);
    }

    /**
     * @param SiteInterface $site
     *
     * @return Collection
     */
    public function findBySite(SiteModelInterface $site)
    {
        return $this->model
            ->where(Model::ATTR_SITE, '=', $site->getId())
            ->orderBy(Model::ATTR_NAME, 'asc')
            ->get();
    }

    public function save(GroupModelInterface $group)
    {
        return $group->save();
    }
}
