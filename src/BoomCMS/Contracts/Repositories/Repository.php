<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Foundation\Database\Model;

interface Repository
{
    /**
     * Delete a single model or multiple models by ID
     *
     * @param array|Model
     *
     * @return $this
     */
    public function delete($param): Repository;

    /**
     * Returns the model with the given ID.
     *
     * @param int|array $modelId
     *
     * @return null|Model
     */
    public function find($modelId);

    /**
     * Save the given model
     *
     * @param Model $model
     *
     * @return Model
     */
    public function save($model): Model;
}
