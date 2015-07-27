<?php

namespace BoomCMS\Core\Tag\Finder;

use BoomCMS\Core\Finder\Finder as BaseFinder;
use BoomCMS\Core\Models\Tag as Model;
use BoomCMS\Core\Tag\Tag;

class Finder extends BaseFinder
{
    public function __construct()
    {
        $this->query = Model::query();
    }

    public function find()
    {
        $model = parent::find();

        return new Tag($model->toArray());
    }

    public function findAll()
    {
        $models = parent::findAll();
        $tags = [];

        foreach ($models as $m) {
            $tags[] = new Tag($m->toArray());
        }

        return $tags;
    }
}
