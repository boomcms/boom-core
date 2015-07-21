<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Core\Theme\Theme;
use BoomCMS\Core\Page\Finder;

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

    public function countPages()
    {
        $finder = new Finder\Finder();
        $finder->addFilter(new Finder\Template($this));

        return $finder->count();
    }

    public function fileExists()
    {
        return View::exists($this->getFullFilename());
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
        $theme = Config::get('boomcms.themes.' . $this->getThemeName() . '.*');
        $template = Config::get('boomcms.themes.' . $this->getThemeName() . '.' . $this->getFilename());

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

    public function getTagGroupSuggestions()
    {
        $config = $this->getConfig();

        return (isset($config['tagGroups']) && is_array($config['tagGroups'])) ?
            $config['tagGroups']
            : [];
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
        return $this->getTheme()->getName() . '::templates.' . $this->getFilename();
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
