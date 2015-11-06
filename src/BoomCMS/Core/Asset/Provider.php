<?php

namespace BoomCMS\Core\Asset;

use BoomCMS\Core\Asset\Asset;
use BoomCMS\Database\Models\Asset as Model;

class Provider
{
    protected $cache = [];

    public function findById($id)
    {
        return $this->findAndCache(Model::withLatestVersion()->find($id));
    }

    public function findByVersionId($versionId)
    {
        $model = Model::withVersion($versionId)->first();

        return $model ? new Asset($model->toArray()) : new Asset();
    }

    private function findAndCache(Model $model = null)
    {
        if (!$model) {
            return;
        }

        if ($model->id) {
            $this->cache[$model->id] = $model;
        }

        return new Asset($model->toArray());
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
