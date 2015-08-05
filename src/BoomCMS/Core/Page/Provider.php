<?php

namespace BoomCMS\Core\Page;

use BoomCMS\Database\Models\Page as Model;

/**
 * TODO: Need to not return deleted / invisible pages by default with option to show a hidden page.
 *
 * This should probably be done by providing an editor instance to the consuctor
 * with a boolean flag to change whether hidden pages are returned.
 *
 */

class Provider
{
    /**
     *
     * @var array
     */
    protected $cache = [
        'id' => [],
        'uri' => [],
        'internal_name' => [],
        'primary_uri' => [],
    ];

    private function cache(Page $page)
    {
        $this->cache['id'][$page->getId()] = $page;

        return $page;
    }

    public function create(array $attrs = [])
    {
        $model = Model::create($attrs);

        return $this->cache(new Page($model->toArray()));
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
        if ( !isset($this->cache['uri'][$uri])) {
            $finder = new Finder\Finder();
            $finder->addFilter(new Finder\Uri($uri));
            $page = $finder->find();

            $this->cache($page);
            $this->cache['uri'][$uri] = $page;
        }

        return $this->cache['uri'][$uri];
    }

    public function findRelatedTo(Page $page)
    {
        $finder = new Finder\Finder();
        $finder->addFilter(new Finder\RelatedTo($page));

        return $finder->findAll();
    }

    private function findAndCache(Model $model = null)
    {
        if ($model) {
            return $this->cache(new Page($model->toArray()));
        }

        return new Page([]);
    }

    public function save(Page $page)
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
