<?php

namespace BoomCMS\Foundation;

use BoomCMS\Contracts\Repositories\Repository as RepositoryInterface;
use BoomCMS\Foundation\Database\Model;

abstract class Repository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Delete a single model or multiple models by ID
     *
     * @param array|Model
     *
     * @return $this
     */
    public function delete($param): RepositoryInterface
    {
        (is_array($param)) ? $this->model->destroy($param) : $param->delete();

        return $this;
    }

    /**
     * Returns the model with the given ID.
     *
     * @param int|array $modelId
     *
     * @return null|Model
     */
    public function find($modelId)
    {
        return $this->model->find($modelId);
    }

    /**
     * Save the given model
     *
     * @param Model $model
     *
     * @return Model
     */
    public function save($model): Model
    {
        $model->save();

        return $model;
    }
}
