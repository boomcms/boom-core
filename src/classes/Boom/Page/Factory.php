<?php

namespace Boom\Page;

use Boom\Model\Page as Model;
use Boom\Page\Finder;

class Provider
{
    public function findById($id)
    {
        return new Page(Model::find($id));
    }

    public function findByInternalName($name)
    {
        return new Page(Model::where('internal_name', '=', $name)->get());
    }

    public function findByPrimaryUri($uri)
    {
        return new Page(Model::where('primary_uri', '=', $uri)->get());
    }

    public function findByUri($uri)
    {
        $finder = new Finder();

        return $finder
            ->addFilter(new Finder\Filter\Uri($uri))
            ->find();
    }
}
