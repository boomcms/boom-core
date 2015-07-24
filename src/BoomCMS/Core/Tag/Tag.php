<?php

namespace BoomCMS\Core\Tag;

class Tag
{
    /**
	 *
	 * @var array
	 */
    protected $attrs;

    public function __construct($attrs = [])
    {
        $this->attrs = $attrs;
    }

    public function delete()
    {
        if ($this->loaded()) {
            $this->model->delete();
        }

        return $this;
    }

    public function get($key)
    {
        return isset($this->attrs[$key]) ? $this->attrs[$key] : null;
    }

    public function getGroup()
    {
        return $this->get('group');
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function getSlug()
    {
        return $this->get('slug');
    }

    public function loaded()
    {
        return $this->getId() > 0;
    }

    public function setName($name)
    {
        $this->attrs['name'] = $name;

        return $this;
    }

    public function setSlug($slug)
    {
        $this->attrs['slug'] = $slug;

        return $this;
    }
}
