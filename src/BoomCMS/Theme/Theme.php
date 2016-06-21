<?php

namespace BoomCMS\Theme;

class Theme
{
    protected $name;

    protected $themesDir = '/boomcms/themes';

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getConfigDirectory()
    {
        return $this->getDirectory().DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'config';
    }

    public function getDirectory()
    {
        return $this->getThemesDirectory().DIRECTORY_SEPARATOR.$this->getName();
    }

    public function getPublicDirectory()
    {
        return $this->getDirectory().DIRECTORY_SEPARATOR.'public';
    }

    public function getTemplateDirectory()
    {
        return $this->getViewDirectory().DIRECTORY_SEPARATOR.'templates';
    }

    public function getThemesDirectory()
    {
        return storage_path().$this->themesDir;
    }

    public function getViewDirectory()
    {
        return $this->getDirectory().DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'views';
    }

    public function getName()
    {
        return $this->name;
    }
}
