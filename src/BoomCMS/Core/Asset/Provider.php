<?php

namespace BoomCMS\Core\Asset;

use BoomCMS\Core\Asset\Helpers\Type;
use BoomCMS\Database\Models\Asset as Model;
use Illuminate\Support\Facades\DB;

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

        $type = Type::numericTypeToClass($model->type) ?: 'Invalid';
        $classname = "\BoomCMS\Core\Asset\\Type\\".$type;

        return $model ? new $classname($model->toArray()) : new $classname();
    }

    /**
     * Returns an array of the asset types which exist in the database.
     *
     * @return array
     */
    public function getStoredTypes()
    {
        $typesAsNumbers = DB::table('assets')->distinct()->lists('type');

        $typesAsStrings = [];

        foreach ($typesAsNumbers as $type) {
            $type = Type::numericTypeToClass($type);

            if ($type) {
                $typesAsStrings[] = $type;
            }
        }

        return $typesAsStrings;
    }

    public static function createFromType($type)
    {
        $model = new Model_Asset();
        $model->type = $type;

        return static::fromModel($model);
    }

    private function findAndCache(Model $model = null)
    {
        if (!$model) {
            return;
        }

        if ($model->id) {
            $this->cache[$model->id] = $model;
        }

        $type = Type::numericTypeToClass($model->type) ?: 'Invalid';
        $classname = "\BoomCMS\Core\Asset\\Type\\".$type;

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
