<?php

namespace Boom\Group;

use Boom;

class Finder extends Boom\Finder\Finder
{
    public function __construct()
    {
        $this->_query = \ORM::factory('Group');
    }

    public function find()
    {
        $model = parent::find();

        return new Group($model);
    }

    public function findAll()
    {
        $groups = parent::findAll()->as_array();

        array_walk($groups, function(&$group) {
            $group = new Group($group);
        });

        return $groups;
    }
}
