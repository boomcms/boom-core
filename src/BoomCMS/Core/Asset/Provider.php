<?php

namespace BoomCMS\Core\Asset;

use BoomCMS\Core\Model\Asset as Model;

class Provider
{
    public static function byId($id)
    {
        return $this->findAndCache(Model::find($id));
    }

    public static function createFromType($type)
    {
        $model = new Model_Asset();
        $model->type = $type;

        return static::fromModel($model);
    }

    private function findAndCache(Model $model)
    {
        if ($model->id) {
            $this->cache[$model->id] = $model;
        }

        $type = Type::numericTypeToClass($model->type) ?: 'Invalid';
        $classname = "\BoomCMS\Core\Asset\\Type\\" . $type;

        return new $classname($model->toArray());
    }

    public function save(Asset $asset)
    {

    }
}
