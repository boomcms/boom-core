<?php

namespace BoomCMS\Contracts;

use BoomCMS\Contracts\Models\Site;
use Illuminate\Database\Eloquent\Builder;

interface SingleSiteInterface
{
    const ATTR_SITE = 'site_id';

    /**
     * @return Site
     */
    public function getSite();

    /**
     * @param Builder $query
     * @param Site $site
     *
     * @return Builder
     */
    public function scopeWhereSiteIs(Builder $query, Site $site);
}
