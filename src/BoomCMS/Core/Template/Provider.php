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
        $model = Model::find($id);
        $attrs = $model ? $model->toArray() : [];

        return new Template($attrs);
    }

    public function findByThemeAndFilename($theme, $filename)
    {
        $model = Model::where('filename', '=', $filename)
            ->where('theme', '=', $theme)
            ->first();

        return new Template($model->toArray());
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
