<?php

namespace BoomCMS\Tests\Asset\Finder;

use BoomCMS\Database\Models\Site;
use BoomCMS\Core\Asset\Finder\Site as SiteFilter;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class SiteTest extends AbstractTestCase
{
    public function testFiltersBySite()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;
        
        $query = m::mock(Builder::class);
        $siteFilter = new SiteFilter($site);

        $query
            ->shouldReceive('join')
            ->once()
            ->with('asset_site', 'asset.id', '=', 'asset_site.asset_id')
            ->andReturnSelf();

        $query
            ->shouldReceive('where')
            ->once()
            ->with('asset_site.site_id', '=', $site->getId());

        $siteFilter->execute($query);
    }
}
