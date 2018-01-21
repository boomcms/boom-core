<?php

namespace BoomCMS\Foundation;

use BoomCMS\Foundation\Database\Model;
use Illuminate\Support\Traits\Macroable;

abstract class Repository
{
    use Macroable;

    /**
     * @var Model
     */
    protected $model;

    public function delete($param): Repository
    {
        (is_array($param)) ? $this->model->destroy($param) : $param->delete();

        return $this;
    }

    public function find($modelId)
    {
        return $this->model->find($modelId);
    }

    public function save($model): Model
    {
        $model->save();

        return $model;
    }
}
