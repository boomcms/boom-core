<?php

namespace Boom\Person;

class Finder extends \Boom\Finder
{
    public function __construct()
    {
        $this->_query = \ORM::factory('Person');
    }

    public function find()
    {
        $model = parent::find();

        return new \Boom\Person($model);
    }

    public function findAll()
    {
        $people = parent::findAll()->as_array();

        return new \Boom\ArrayCallbackIterator($people, function ($person) {
            return new \Boom\Person($person);
        });
    }
}
