<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Core\Page;

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
        return View::exists($this->getFullFilename());
    }

    public function get($key)
    {
        return isset($this->attrs[$key]) ? $this->attrs[$key] : null;
    }

    public function getDescription()
    {
        return $this->get('description');
    }

    public function getFilename()
    {
        return $this->get('filename');
    }

    public function getFullFilename()
    {
        return static::DIRECTORY . $this->getFilename();
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
