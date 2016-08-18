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
     * @param int $templateId
     *
     * @return TemplateInterface
     */
    public function deleteById($templateId);

    /**
     * @param int $templateId
     *
     * @return TemplateInterface
     */
    public function find($templateId);

    public function findAll();

    /**
     * @param string $theme
     * @param string $filename
     *
     * @return TemplateInterface
     */
    public function findByThemeAndFilename($theme, $filename);

    /**
     * @return array
     */
    public function findValid();

    /**
     * @param TemplateInterface $model
     *
     * @return $this
     */
    public function save(TemplateInterface $model);
}
