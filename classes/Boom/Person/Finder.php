<?php

namespace Boom\Person;

use Boom;

class Finder extends Boom\Finder\Finder
{
    public function __construct()
    {
        $this->_query = \ORM::factory('Person');
    }

    public function find()
    {
        $model = parent::find();

        return new Person($model);
    }

    public function findAll()
    {
        $people = parent::findAll()->as_array();

        return new \Boom\ArrayCallbackIterator($people, function ($person) {
            return new Person($person);
        });
    }
}
