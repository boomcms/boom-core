<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Group as GroupModelInterface;
use BoomCMS\Contracts\Models\Site as SiteModelInterface;
use BoomCMS\Contracts\Repositories\Group as GroupRepositoryInterface;
use BoomCMS\Database\Models\Group as Model;

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
        $group->delete();

        return $this;
    }

    public function findAll()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * @param SiteInterface $site
     */
    public function findBySite(SiteModelInterface $site)
    {
        
    }

    /**
     * @param SiteModelInterface $site
     * @param array $groupIds
     */
    public function findBySiteExcluding(SiteModelInterface $site, array $groupIds)
    {
        ;
    }

    public function save(GroupModelInterface $group)
    {
        return $group->save();
    }
}
