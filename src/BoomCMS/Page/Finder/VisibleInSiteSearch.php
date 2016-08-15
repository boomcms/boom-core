<?php

namespace BoomCMS\Page\Finder;

use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class VisibleInSiteSearch extends Filter
{
    public function execute(Builder $query)
    {
        return $query->where('internal_indexing', '=', true);
    }
}
