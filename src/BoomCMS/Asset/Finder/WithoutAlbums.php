<?php

namespace BoomCMS\Asset\Finder;

use BoomCMS\Foundation\Finder\Filter as BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class WithoutAlbums extends BaseFilter
{
    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function build(Builder $query)
    {
        return $query->doesntHave('albums');
    }
}
