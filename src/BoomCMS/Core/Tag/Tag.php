<?php

namespace BoomCMS\Core\Tag;

use BoomCMS\Support\Traits\Comparable;

class Tag
{
    use Comparable;

    /**
     * @var array
     */
    protected $attributes;

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
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
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
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
        $this->attributes['name'] = $name;

        return $this;
    }

    public function setSlug($slug)
    {
        $this->attributes['slug'] = $slug;

        return $this;
    }
}
