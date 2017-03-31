<?php

namespace BoomCMS\Tests\Integration\Asset;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\AssetVersion;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Auth;
use Mockery as m;

class AssetTest extends AbstractTestCase
{
    protected $asset;

    protected $url = 'http://localhost/asset/1';

    public function setUp()
    {
        parent::setUp();

        $this->asset = m::mock(Asset::class)->makePartial();
        $this->asset->{Asset::ATTR_ID} = 1;

        $this->asset->setVersion(new AssetVersion());

        $this->app['router']->bind('asset', function () {
            return $this->asset;
        });
    }

    public function test404IfAssetFileNotFound()
    {
        AssetFacade::shouldReceive('exists')->once()->andReturn(false);

        $this->call('GET', $this->url);

        $this->assertResponseStatus(404);
    }

    public function test401IfAssetNotPublicAndUserIsGuest()
    {
        $this->asset->shouldReceive('isPublic')->andReturn(false);

        AssetFacade::shouldReceive('exists')->once()->andReturn(true);
        Auth::shouldReceive('check')->andReturn(false);

        $this->call('GET', $this->url);

        $this->assertResponseStatus(401);
    }

    protected function assetIsAccessible()
    {
        $this->asset->shouldReceive('isPublic')->andReturn(true);

        AssetFacade::shouldReceive('exists')->once()->andReturn(true);
    }
}
