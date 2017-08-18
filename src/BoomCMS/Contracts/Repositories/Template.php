<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Template as TemplateInterface;

interface Template extends Repository
{
    /**
     * @param array $attrs
     *
     * @return TemplateInterface
     */
    public function create(array $attrs);

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
}
