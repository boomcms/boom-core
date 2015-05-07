<?php

namespace BoomCMS\Core\Tag;

use \Model_Tag as Model_Tag;

abstract class Factory
{
    public static function byId($id)
    {
        return new Tag(new Model_Tag($id));
    }

    public static function byName($name)
    {
        return new Tag(new Model_Tag(['name' => $name]));
    }

    public static function findByNameAndGroup($name, $group= null)
    {
        return new Tag(new Model_Tag(['name' => $name, 'group' => $group]));
    }

    public static function findOrCreateByNameAndGroup($name, $group = null)
    {
        // Ensure group is null if an empty string is passed.
        $group = $group ?: null;

        $model = new Model_Tag(['name' => $name, 'group' => $group]);

        if ( ! $model->loaded()) {
            $model
                ->set('name', $name)
                ->set('group', $group)
                ->create();
        }

        return new Tag($model);
    }
}
