<?php

namespace BoomCMS\Core\Tag;

use Boom;

class Finder extends Boom\Finder\Finder
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
