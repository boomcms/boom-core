<?php

namespace BoomCMS\Tests\Assets;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\Site;
use BoomCMS\Http\Controllers\Assets\AssetManager as Controller;
use BoomCMS\Support\Facades\Site as SiteFacade;
use BoomCMS\Tests\Http\Controllers\BaseControllerTest;
use Illuminate\Http\Request;
use Mockery as m;

class AssetManagerTest extends BaseControllerTest
{
    /**
     * @var string
     */
    protected $className = Controller::class;

    public function testAddSites()
    {
        $siteIds = [1, 2];
        $sites = [new Site(), new Site()];
        $request = new Request(['sites' => $siteIds]);
        $asset = m::mock(Asset::class);

        $asset->shouldReceive('addSites')->once()->with($sites);

        SiteFacade::shouldReceive('find')->with($siteIds)->andReturn($sites);

        $this->controller->addSites($request, $asset);
    }

    public function testAddSitesDoesNotQueryForSitesIfNoIdsGiven()
    {
        $request = new Request();
        $asset = m::mock(Asset::class);

        SiteFacade::shouldReceive('find')->never();

        $this->controller->addSites($request, $asset);
    }

    public function testRemoveSite()
    {
        $site = new Site();
        $asset = m::mock(Asset::class);

        $asset
            ->shouldReceive('removeSite')
            ->once()
            ->with($site);

        $this->controller->removeSite($asset, $site);
    }
}
