<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Database\Models\Asset;
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

    public function build(Builder $query)
    {
        return $query->where(Asset::ATTR_SITE, $this->site->getId());
    }
}
