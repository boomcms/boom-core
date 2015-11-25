<?php

namespace BoomCMS\Repositories;

use BoomCMS\Core\Page\Finder;
use BoomCMS\Database\Models\Page as Model;

class Page
{
    /**
     * @var array
     */
    protected $cache = [
        'id'            => [],
        'uri'           => [],
        'internal_name' => [],
        'primary_uri'   => [],
    ];

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

    public function delete(Page $page)
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

    public function findByPrimaryUri($uri)
    {
        return $this->model->where(Model::ATTR_PRIMARY_URI, '=', $uri)->first();
    }

    public function findByUri($uri)
    {
        $finder = new Finder\Finder();
        $finder->addFilter(new Finder\Uri($uri));

        return $page = $finder->find();
    }

    public function save(Model $page)
    {
        return $page->save();
    }
}
