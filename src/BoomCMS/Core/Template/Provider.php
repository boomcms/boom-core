<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Core\Models\Template as Model;

class Provider
{
    public function deleteById($id)
    {
        Model::destroy($id);

        return $this;
    }

    public function findAll()
    {
        $models = Model::all();
        $templates = [];

        foreach ($models as $model) {
            $templates[] = new Template($model->toArray());
        }

        return $templates;
    }

    public function findById($id)
    {
        return new Template(Model::find($id)->toArray());
    }

    public function findByFilename($filename)
    {
        return new Template(Model::where('filename', '=', $filename)->first());
    }

    public function save(Template $template)
    {
        $model = Model::find($template->getId());

        if ($model) {
            $model->filename = $template->getFilename();
            $model->description = $template->getDescription();
            $model->name = $template->getName();

            $model->save();
        }

        return $this;
    }
}
