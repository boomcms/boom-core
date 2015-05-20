<?php

namespace BoomCMS\Core\Group;

use BoomCMS\Core\Finder\Finder as BaseFinder;

class Finder extends BaseFinder
{
    public function __construct()
    {
        $this->_query = \ORM::factory('Group')
            ->where('deleted', '=', false);
    }

    public function find()
    {
        $model = parent::find();

        return new Group($model);
    }

    public function findAll()
    {
        $groups = parent::findAll()->as_array();

        array_walk($groups, function (&$group) {
            $group = new Group($group);
        });

        return $groups;
    }
}
