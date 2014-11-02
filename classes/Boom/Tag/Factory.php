<?php

namespace Boom\Tag;

use \Model_Tag as Model_Tag;

abstract class Factory
{
    public static function byId($id)
    {
        return new Tag(new Model_Tag($id));
    }

    public static function byName($name)
    {
        return new Tag(new Model_Tag(array('name' => $name)));
    }

    public static function findOrCreateByName($name)
    {
        $model = new Model_Tag(array('name' => $name));

        if ( ! $model->loaded()) {
            $model
                ->set('name', $name)
                ->create();
        }

        return new Tag($model);
    }
}
