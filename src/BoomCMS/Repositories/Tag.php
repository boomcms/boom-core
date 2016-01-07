<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Contracts\Models\Tag as TagInterface;
use BoomCMS\Contracts\Repositories\Tag as TagRepositoryInterface;
use BoomCMS\Database\Models\Tag as Model;

class Tag implements TagRepositoryInterface
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
     * @param SiteInterface $site
     * @param string        $name
     * @param string        $group
     *
     * @return TagInterface
     */
    public function create(SiteInterface $site, $name, $group)
    {
        return $this->model->create([
            Model::ATTR_SITE  => $site,
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
     * @param SiteInterface $site
     * @param string        $name
     *
     * @return TagInterface
     */
    public function findByName(SiteInterface $site, $name)
    {
        return $this->model
            ->whereSiteIs($site)
            ->where(Model::ATTR_NAME, '=', $name)
            ->first();
    }

    /**
     * @param SiteInterface $site
     * @param string        $name
     * @param string        $group
     *
     * @return TagInterface
     */
    public function findByNameAndGroup(SiteInterface $site, $name, $group = null)
    {
        return $this->model
            ->whereSiteIs($site)
            ->where(Model::ATTR_NAME, '=', $name)
            ->where(Model::ATTR_GROUP, '=', $group)
            ->first();
    }

    /**
     * @param SiteInterface $site
     * @param string        $slug
     * @param string        $group
     *
     * @return TagInterface
     */
    public function findBySlugAndGroup(SiteInterface $site, $slug, $group = null)
    {
        return $this->model
            ->whereSiteIs($site)
            ->where(Model::ATTR_SLUG, '=', $slug)
            ->where(Model::ATTR_GROUP, '=', $group)
            ->first();
    }

    /**
     * @param SiteInterface $site
     * @param string        $name
     * @param string        $group
     *
     * @return TagInterface
     */
    public function findOrCreate(SiteInterface $site, $name, $group = null)
    {
        // Ensure group is null if an empty string is passed.
        $group = $group ?: null;
        $tag = $this->findByNameAndGroup($site, $name, $group);

        return $tag ?: $this->create($site, $name, $group);
    }
}
