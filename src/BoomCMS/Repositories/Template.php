<?php

namespace BoomCMS\Repositories;

use BoomCMS\Core\Template\Template as TemplateObject;
use BoomCMS\Database\Models\Template as Model;

class Template
{
    public function deleteById($id)
    {
        Model::destroy($id);

        return $this;
    }

    public function findAll()
    {
        $models = Model::query()
            ->orderBy('theme', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $templates = [];

        foreach ($models as $model) {
            $templates[] = new TemplateObject($model->toArray());
        }

        return $templates;
    }

    public function findById($id)
    {
        $model = Model::find($id);
        $attrs = $model ? $model->toArray() : [];

        return new TemplateObject($attrs);
    }

    public function findByThemeAndFilename($theme, $filename)
    {
        $model = Model::where('filename', '=', $filename)
            ->where('theme', '=', $theme)
            ->first();

        return $model ? new TemplateObject($model->toArray()) : new Template([]);
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
