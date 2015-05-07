<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Core\Models\Template as Model;

class Provider
{
    public function findAll()
    {
        $finder = new Finder();

        return $finder
            ->setOrderBy('name', 'asc')
            ->findAll();
    }

    public function findById($id)
    {
        return new Template(Model::get($id)->toArray());
    }

    public function findByFilename($filename)
    {
        return new Template(Model::where('filename', '=', $filename)->first());
    }
}
