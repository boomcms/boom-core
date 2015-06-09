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

    public function execute(Builder $query)
    {
        return $query->where('parent_id', '=', $this->parentId);
    }
}
