<?php

namespace Boom\Tag;

class Finder extends \Boom\Finder
{
    public function __construct()
    {
        $this->_query = \ORM::factory('Tag');
    }

    public function find()
    {
        $model = parent::find();

        return new Tag($model);
    }

    public function findAll()
    {
        $tags = parent::findAll();

        return new Finder\Result($tags);
    }
}
