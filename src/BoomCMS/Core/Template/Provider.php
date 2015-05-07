<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Core\Models\Template as Model;

class Provider
{
    public function deleteById($id)
    {

    }

    public function findAll()
    {
        $models = Model::all();
        $templates = [];

        foreach ($models as $model) {
            $templates = new Template($model->toArray());
        }

        return $templates;
    }

    public function findById($id)
    {
        return new Template(Model::get($id)->toArray());
    }

    public function findByFilename($filename)
    {
        return new Template(Model::where('filename', '=', $filename)->first());
    }
}
