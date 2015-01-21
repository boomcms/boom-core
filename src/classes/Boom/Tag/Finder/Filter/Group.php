<?php

namespace Boom\Tag\Finder\Filter;

class Group extends \Boom\Finder\Filter
{
    protected $gorup;

    public function __construct($group)
    {
        $this->group = $group;
    }

    public function execute(\ORM $query)
    {
        return $query->where('group', '=', $this->group);
    }
}
