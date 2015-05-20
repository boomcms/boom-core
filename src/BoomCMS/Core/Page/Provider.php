<?php

namespace BoomCMS\Core\Page;

use BoomCMS\Core\Editor\Editor;
use BoomCMS\Core\Model\Page as Model;
use BoomCMS\Core\Page\Finder\Finder;

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

    /**
     *
     * @var Editor
     */
    protected $editor;

    public function __construct(Editor $editor)
    {
        $this->editor = $editor;
    }

    private function cache(Page $page)
    {
        $this->cache['id'][$page->getId()] = $page;
    }

    public function findById($id)
    {
        return $this->findAndCache(Model::find($id));
    }

    public function findByInternalName($name)
    {
        return $this->findAndCache(Model::where('internal_name', '=', $name)->get());
    }

    public function findByPrimaryUri($uri)
    {
        return $this->findAndCache(Model::where('primary_uri', '=', $uri)->get());
    }

    public function findByUri($uri)
    {
        if ( !isset($this->cache['uri'][$uri])) {
            $finder = new Finder($this->editor);
            $finder->addFilter(new Finder\Uri($uri));
            $page = $finder->find();

            $this->cache($page);
            $this->cache['uri'][$uri] = $page;
        }

        return $this->cache['uri'][$uri];
    }

    private function findAndCache(Model $model)
    {
        if ($model->id) {
            $this->cache[$model->id] = $model;
        }

        return new Page($model->toArray());
    }

    public function save(Page $page)
    {

    }
}
