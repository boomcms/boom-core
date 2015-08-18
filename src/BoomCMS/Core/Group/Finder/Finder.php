<?php

namespace BoomCMS\Core\Group\Finder;

use BoomCMS\Foundation\Finder\Finder as BaseFinder;
use BoomCMS\Core\Group\Group;
use BoomCMS\Database\Models\Group as Model;

class Finder extends BaseFinder
{
    public function __construct()
    {
        $this->query = Model::query();
    }

    public function find()
    {
        $model = parent::find();

        return new Group($model);
    }

    public function findAll()
    {
        $groups = parent::findAll();
        $return = [];

        foreach ($groups as $group) {
            $return[] = new Group($group->toArray());
        }

        return $return;
    }
}
