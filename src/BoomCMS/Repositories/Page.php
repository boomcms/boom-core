<?php

namespace BoomCMS\Repositories;

use BoomCMS\Core\Page\Finder;
use BoomCMS\Core\Page\Page as PageObject;
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

    private function cache(PageObject $page)
    {
        $this->cache['id'][$page->getId()] = $page;

        return $page;
    }

    public function create(array $attrs = [])
    {
        $model = Model::create($attrs);

        return $this->cache(new PageObject($model->toArray()));
    }

    public function delete(Page $page)
    {
        unset($this->cache['id'][$page->getId()]);
        Model::destroy($page->getId());
    }

    public function findById($id)
    {
        return $this->findAndCache(Model::find($id));
    }

    public function findByInternalName($name)
    {
        return $this->findAndCache(Model::where('internal_name', '=', $name)->first());
    }

    public function findByParent(Page $parent)
    {
        $finder = new Finder\Finder();
        $finder->addFilter(new Finder\ParentPage($parent));

        return $finder->findAll();
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
            return $this->cache(new PageObject($model->toArray()));
        }

        return new PageObject();
    }

    public function save(PageObject $page)
    {
        if ($page->loaded()) {
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
