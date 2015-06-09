<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Core\Theme\Theme;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

class Template
{
    /**
     *
     * @var array
     */
    protected $attrs;

    public function __construct(array $attrs)
    {
        $this->attrs = $attrs;
    }

    public function fileExists()
    {
        return View::exists($this->getViewName());
    }

    public function get($key)
    {
        return isset($this->attrs[$key]) ? $this->attrs[$key] : null;
    }

    public function getChunks()
    {
        $config = $this->getConfig();

        return (isset($config['chunks']) && is_array($config['chunks'])) ? $config['chunks'] : [];
    }

    public function getConfig()
    {
        $theme = Config::get('themes.' . $this->getThemeName() . '.*');
        $template = Config::get('themes.' . $this->getThemeName() . '.' . $this->getName());

        return array_merge((array) $theme, (array) $template);
    }

    public function getDescription()
    {
        return $this->get('description');
    }

    public function getFilename()
    {
        return $this->get('filename');
    }

    /**
     *
     * @return Theme
     */
    public function getTheme()
    {
        return new Theme($this->getThemeName());
    }

    public function getThemeName()
    {
        return $this->get('theme');
    }

    public function getFullFilename()
    {
        return $this->getTheme()->getTemplateDirectory() . DIRECTORY_SEPARATOR . $this->getFilename();
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function getView()
    {
        return ($this->fileExists()) ? View::make($this->getFullFilename()) : View::make('boom::templates.default');
    }

    public function getViewName()
    {
        return $this->getThemeName() . ':' . 'templates.' . $this->getFilename();
    }

    public function loaded()
    {
        return $this->getId() > 0;
    }

    public function setDescription($description)
    {
        $this->attrs['description'] = $description;

        return $this;
    }

    public function setFilename($filename)
    {
        $this->attrs['filename'] = $filename;

        return $this;
    }

    public function setName($name)
    {
        $this->attrs['name'] = $name;

        return $this;
    }
}
