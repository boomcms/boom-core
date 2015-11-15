<?php

namespace BoomCMS\Core\Tag;

use BoomCMS\Support\Traits\Comparable;
use BoomCMS\Support\Traits\HasId;

class Tag
{
    use Comparable;
    use HasId;

    /**
     * @var array
     */
    protected $attributes;

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function get($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    public function getGroup()
    {
        return $this->get('group');
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function getSlug()
    {
        return $this->get('slug');
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->attributes['name'] = $name;

        return $this;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->attributes['slug'] = $slug;

        return $this;
    }
}
