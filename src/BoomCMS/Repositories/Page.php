<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Core\Page\Finder;
use BoomCMS\Database\Models\Page as Model;
use BoomCMS\Support\Facades\Router;

class Page
{
    /**
     * @var Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $attrs = [])
    {
        return Model::create($attrs);
    }

    public function delete(Model $page)
    {
        $page->delete();

        return $this;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findByInternalName($name)
    {
        return $this->model->where(Model::ATTR_INTERNAL_NAME, '=', $name)->first();
    }

    public function findByParentId($parentId)
    {
        $finder = new Finder\Finder();
        $finder->addFilter(new Finder\ParentId($parentId));

        return $finder->findAll();
    }

    /**
     * @param string $uri
     *
     * @return null|Model
     */
    public function findByPrimaryUri($uri)
    {
        $site = Router::getActiveSite();

        return $this->findBySiteAndPrimaryUri($site, $uri);
    }

    /**
     * @param SiteInterface $site
     * @param string $uri
     *
     * @return null|Model
     */
    public function findBySiteAndPrimaryUri(SiteInterface $site, $uri)
    {
        return $this->model
            ->where(Model::ATTR_SITE, '=', $site->getId())
            ->where(Model::ATTR_PRIMARY_URI, '=', $uri)
            ->first();
    }

    public function findByUri($uri)
    {
        $finder = new Finder\Finder();
        $finder->addFilter(new Finder\Uri($uri));

        return $page = $finder->find();
    }

    public function save(Model $page)
    {
        $page->save();

        return $page;
    }
}
