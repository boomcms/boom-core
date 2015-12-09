<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Template as TemplateInterface;
use BoomCMS\Database\Models\Template as TemplateModel;

class Template
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
     * @param int $id
     *
     * @return $this
     */
    public function delete(TemplateModel $template)
    {
        $template->delete();

        return $this;
    }

    /**
     * @param int $id
     *
     * @return TemplateInterface
     */
    public function find($id)
    {
        return $this->model->find($id);
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
     * @param TemplateInterface $model
     *
     * @return $this
     */
    public function save(TemplateInterface $model)
    {
        $model->save();

        return $this;
    }
}
