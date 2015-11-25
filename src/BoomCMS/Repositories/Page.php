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

    private function cache(Model $page = null)
    {
        $this->cache['id'][$page->getId()] = $page;

        return $page;
    }

    public function create(array $attrs = [])
    {
        $model = Model::create($attrs);

        return $this->cache($model);
    }

    public function delete(Page $page)
    {
        unset($this->cache['id'][$page->getId()]);
        Model::destroy($page->getId());
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findByInternalName($name)
    {
        return $this->findAndCache(Model::where(Model::ATTR_INTERNAL_NAME, '=', $name)->first());
    }

    public function findByParentId($parentId)
    {
        $finder = new Finder\Finder();
        $finder->addFilter(new Finder\ParentId($parentId));

        return $finder->findAll();
    }

    public function findByPrimaryUri($uri)
    {
        return $this->findAndCache(Model::where('primary_uri', '=', $uri)->first());
    }

    public function findByUri($uri)
    {
        if (!isset($this->cache['uri'][$uri])) {
            $finder = new Finder\Finder();
            $finder->addFilter(new Finder\Uri($uri));
            $page = $finder->find();

            $this->cache($page);
            $this->cache['uri'][$uri] = $page;
        }

        return $this->cache['uri'][$uri];
    }

    private function findAndCache(Model $model = null)
    {
        if ($model) {
            return $this->cache($model);
        }
    }

    public function save(PageObject $page)
    {
        if ($page->getId()) {
            $model = isset($this->cache[$page->getId()]) ?
                $this->cache[$page->getId()]
                : Model::find($page->getId());

            $model->update($page->toArray());
        } else {
            $model = Model::create($page->toArray());
            $page->setId($model->id);
        }

        return $page;
    }
}
