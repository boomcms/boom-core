<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class VisibleToSearchEngines extends Filter
{
    public function execute(Builder $query)
    {
        return $query->where('external_indexing', '=', true);
    }
}
