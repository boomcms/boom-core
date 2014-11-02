<?php

namespace Boom\Group;

class Finder extends \Boom\Finder
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

        return new \Boom\ArrayCallbackIterator($groups, function ($group) {
            return new Group($group);
        });
    }
}
