<?php

namespace BoomCMS\Support\Traits;

use BoomCMS\Contracts\Models\Site;
use BoomCMS\Database\Models\Site as SiteModel;
use Illuminate\Database\Eloquent\Builder;

trait SingleSite
{
    /**
     * @var Site
     */
    protected $site;

    public function getSite()
    {
        if ($this->site === null) {
            $this->site = $this->belongsTo(SiteModel::class, 'site_id')->first();
        }

        return $this->site;
    }

    public function scopeWhereSiteIs(Builder $query, Site $site = null)
    {
        if ($site === null) {
            return $query;
        }

        return $query->where('site_id', '=', $site->getId());
    }
}
