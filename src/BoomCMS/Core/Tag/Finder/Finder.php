<?php

namespace BoomCMS\Core\Tag\Finder;

use BoomCMS\Database\Models\Tag as Model;
use BoomCMS\Foundation\Finder\Finder as BaseFinder;

class Finder extends BaseFinder
{
    public function __construct()
    {
        $this->query = Model::query();
    }

    public function find()
    {
        return parent::find();
    }

    public function findAll()
    {
        return parent::findAll();
    }
}
