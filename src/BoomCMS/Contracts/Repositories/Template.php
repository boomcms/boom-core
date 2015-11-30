<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Template as TemplateInterface;

interface Template
{
    /**
     * @param array $attrs
     *
     * @return TemplateInterface
     */
    public function create(array $attrs);

    /**
     * @param int $id
     *
     * @return TemplateInterface
     */
    public function deleteById($id);

    /**
     * @param int $id
     *
     * @return TemplateInterface
     */
    public function find($id);

    public function findAll();

    /**
     * @param string $theme
     * @param string $filename
     *
     * @return TemplateInterface
     */
    public function findByThemeAndFilename($theme, $filename);

    /**
     * @param TemplateInterface $model
     *
     * @return $this
     */
    public function save(TemplateInterface $model);
}
