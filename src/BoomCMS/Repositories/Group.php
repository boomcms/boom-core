<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Site as SiteModelInterface;
use BoomCMS\Contracts\Repositories\Group as GroupRepositoryInterface;
use BoomCMS\Database\Models\Group as Model;
use BoomCMS\Foundation\Repository;
use Illuminate\Database\Eloquent\Collection;

class Group extends Repository implements GroupRepositoryInterface
{
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

    public function findAll()
    {
        return $this->model->orderBy('name', 'asc')->get();
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
}
