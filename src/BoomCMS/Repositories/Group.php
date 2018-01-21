<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Site as SiteModelInterface;
use BoomCMS\Database\Models\Group as Model;
use BoomCMS\Foundation\Repository;
use Illuminate\Support\Collection;

class Group extends Repository
{
    public function __construct(Model $model = null)
    {
        $class = Model::class;

        $this->model = $model ?: new $class();
    }

    public function create($name): Model
    {
        return $this->model->create([
            Model::ATTR_NAME => $name,
        ]);
    }

    public function findAll(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    public function findBySite(SiteModelInterface $site): Collection
    {
        return $this->model
            ->where(Model::ATTR_SITE, '=', $site->getId())
            ->orderBy(Model::ATTR_NAME, 'asc')
            ->get();
    }
}
