<?php

namespace BoomCMS\Core\Tag\Finder;

use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class Group extends Filter
{
    protected $gorup;

    public function __construct($group)
    {
        $this->group = $group;
    }

    public function execute(Builder $query)
    {
        if ($this->group) {
            return $query->where('group', '=', $this->group);
        } else {
            return $query->whereNull('group');
        }
    }
}
