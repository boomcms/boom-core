<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Site as SiteModelInterface;
use BoomCMS\Contracts\Repositories\Site as SiteRepositoryInterface;
use BoomCMS\Database\Models\Site as Model;

class Site implements SiteRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function create(array $attributes)
    {
        return Model::create($attributes);
    }

    /**
     * @param SiteModelInterface $site
     *
     * @return $this
     */
    public function delete(SiteModelInterface $site)
    {
        $site->delete();

        return $this;
    }

    public function findAll()
    {
        return $this->model->all();
    }

    /**
     * @param int $id
     *
     * @return SiteModelInterface
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * @param SiteModelInterface $site
     *
     * @return SiteModelInterface
     */
    public function save(SiteModelInterface $site)
    {
        return $site->save();
    }
}
