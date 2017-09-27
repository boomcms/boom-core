<?php

namespace BoomCMS\Asset\Finder;

use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Foundation\Finder\Filter as BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class Site extends BaseFilter
{
    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * @param SiteInterface $site
     */
    public function __construct(SiteInterface $site)
    {
        $this->site = $site;
    }

    public function execute(Builder $query)
    {
        return $query
            ->join('asset_site', 'asset.id', '=', 'asset_site.asset_id')
            ->where('asset_site.site_id', '=', $this->site->getId());
    }
}
