<?php

namespace BoomCMS\Core\Asset;

use BoomCMS\Core\Models\Asset as Model;

class Provider
{
    protected $cache = [];

    public function findById($id)
    {
        return $this->findAndCache(Model::find($id));
    }

    public static function createFromType($type)
    {
        $model = new Model_Asset();
        $model->type = $type;

        return static::fromModel($model);
    }

    private function findAndCache(Model $model = null)
    {
        if (! $model) {
            return;
        }

        if ($model->id) {
            $this->cache[$model->id] = $model;
        }

        $type = Type::numericTypeToClass($model->type) ?: 'Invalid';
        $classname = "\BoomCMS\Core\Asset\\Type\\" . $type;

        return new $classname($model->toArray());
    }

    public function save(Asset $asset)
    {
        if ($asset->loaded()) {
            $model = isset($this->cache[$asset->getId()]) ?
                $this->cache[$asset->getId()]
                : Model::find($asset->getId());

            $model->update($asset->toArray());
        } else {
            $model = Model::create($asset->toArray());
            $asset->setId($model->id);
        }

        return $asset;
    }
}
