<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Template as TemplateInterface;
use BoomCMS\Database\Models\Template as TemplateModel;
use BoomCMS\Foundation\Repository;

class Template extends Repository
{
    /**
     * @param TemplateModel $model
     */
    public function __construct(TemplateModel $model)
    {
        $this->model = $model;
    }

    public function create(array $attrs): TemplateInterface
    {
        return $this->model->create($attrs);
    }

    public function findAll()
    {
        return $this->model
            ->orderBy('theme', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function findByThemeAndFilename($theme, $filename)
    {
        return $this->model->where('filename', '=', $filename)
            ->where('theme', '=', $theme)
            ->first();
    }

    public function findValid(): array
    {
        $valid = [];
        $templates = $this->findAll();

        foreach ($templates as $template) {
            if ($template->fileExists()) {
                $valid[] = $template;
            }
        }

        return $valid;
    }
}
