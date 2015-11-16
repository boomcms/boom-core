<?php

namespace BoomCMS\Repositories;

use BoomCMS\Core\Asset\Asset as AssetObject;
use BoomCMS\Database\Models\Asset as Model;

class Asset
{
    protected $cache = [];

    public function findById($id)
    {
        return $this->findAndCache(Model::withLatestVersion()->find($id));
    }

    public function findByVersionId($versionId)
    {
        $model = Model::withVersion($versionId)->first();

        return $model ? new AssetObject($model->toArray()) : new Asset();
    }

    private function findAndCache(Model $model = null)
    {
        if (!$model) {
            return;
        }

        if ($model->id) {
            $this->cache[$model->id] = $model;
        }

        return new AssetObject($model->toArray());
    }

    public function save(AssetObject $asset)
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
