<?php

namespace BoomCMS\Core\Page;

use Boom\Model\Page as Model;
use Boom\Page\Finder;

class Provider
{
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
        $finder = new Finder();
        $finder->addFilter(new Finder\Filter\Uri($uri));

        return $this->findAndCache($finder->find());
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
