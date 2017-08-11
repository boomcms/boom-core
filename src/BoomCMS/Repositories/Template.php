<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Template as TemplateInterface;
use BoomCMS\Database\Models\Template as TemplateModel;
use BoomCMS\Foundation\Repository;

class Template extends Repository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @param TemplateModel $model
     */
    public function __construct(TemplateModel $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $attrs
     *
     * @return TemplateInterface
     */
    public function create(array $attrs)
    {
        return $this->model->create($attrs);
    }

    /**
     * Delete the given template.
     *
     * @param TemplateModel $template
     *
     * @return $this
     */
    public function delete(TemplateModel $template)
    {
        $template->delete();

        return $this;
    }

    /**
     * Returns the template with the given ID.
     *
     * @param int $templateId
     *
     * @return null|TemplateInterface
     */
    public function find($templateId)
    {
        return $this->model->find($templateId);
    }

    public function findAll()
    {
        return $this->model
            ->orderBy('theme', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * @param string $theme
     * @param string $filename
     *
     * @return TemplateInterface
     */
    public function findByThemeAndFilename($theme, $filename)
    {
        return $this->model->where('filename', '=', $filename)
            ->where('theme', '=', $theme)
            ->first();
    }

    /**
     * @return array
     */
    public function findValid()
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

    /**
     * @param TemplateInterface $model
     *
     * @return TemplateInterface
     */
    public function save(TemplateInterface $model)
    {
        $model->save();

        return $model;
    }
}
