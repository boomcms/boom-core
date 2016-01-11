<?php

namespace BoomCMS\Support\Traits;

use BoomCMS\Contracts\Models\Site;
use Illuminate\Database\Eloquent\Builder;

trait SingleSite
{
    public function getSite()
    {
    }

    public function scopeWhereSiteIs(Builder $query, $site)
    {
        return $query->where('site_id', '=', $site->getId());
    }

    public function setSite(Site $site)
    {
    }
}
