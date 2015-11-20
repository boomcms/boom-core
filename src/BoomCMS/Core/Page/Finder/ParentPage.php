<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class ParentPage extends Filter
{
    protected $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function build(Builder $query)
    {
        if ($this->parent->loaded()) {
            list($col, $direction) = $this->parent->getChildOrderingPolicy();

            return $query
                ->where('parent_id', '=', $this->parent->getId())
                ->orderBy($col, $direction);
        } else {
            return $query->whereNull('parent_id');
        }
    }
}
