<?php

namespace BoomCMS\Tests\Support\Traits;

use BoomCMS\Database\Models\Site;
use BoomCMS\Support\Traits\SingleSite;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class SingleSiteTest extends AbstractTestCase
{
    public function testScopeWhereSiteIn()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;

        $single = $this->getMockForTrait(SingleSite::class);
        $query = m::mock(Builder::class);

        $query
            ->shouldReceive('where')
            ->once()
            ->with('site_id', '=', $site->getId());

        $single->scopeWhereSiteIs($query, $site);
    }
}
