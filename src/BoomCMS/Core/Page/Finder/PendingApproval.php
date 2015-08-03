<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Finder\Filter;

use Illuminate\Database\Eloquent\Builder;

class PendingApproval extends Filter
{
    public function build(Builder $query)
    {
        return $query
            ->where('pending_approval', '=', true)
            ->orderBy('version.edited_time', 'desc');
    }
}
