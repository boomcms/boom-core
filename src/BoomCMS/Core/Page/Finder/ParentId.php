<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Finder\Filter;

use Illuminate\Database\Eloquent\Builder;

class ParentId extends Filter
{
    protected $parentId;

    public function __construct($parentId)
    {
        $this->parentId = $parentId;
    }

    public function build(Builder $query)
    {
        if ($this->parentId) {
            return $query->where('parent_id', '=', $this->parentId);
        } else {
            return $query->whereNull('parent_id');
        }
    }
}
