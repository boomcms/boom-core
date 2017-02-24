<?php

namespace BoomCMS\Tests\Integration\Asset;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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

        $this->app['router']->bind('asset', function () {
            return $this->asset;
        });
    }

    public function test404IfAssetFileNotFound()
    {
        $filename = 'test';

        $this->asset->shouldReceive('getFilename')->once()->andReturn($filename);
        File::shouldReceive('exists')->once()->with($filename)->andReturn(false);

        $this->call('GET', $this->url);

        $this->assertResponseStatus(404);
    }

    public function test401IfAssetNotPublicAndUserIsGuest()
    {
        $this->asset->shouldReceive('getFilename')->andReturn('');
        $this->asset->shouldReceive('isPublic')->andReturn(false);

        File::shouldReceive('exists')->andReturn(true);
        Auth::shouldReceive('check')->andReturn(false);

        $this->call('GET', $this->url);

        $this->assertResponseStatus(401);
    }

    protected function assetIsAccessible()
    {
        $this->asset->shouldReceive('getFilename')->andReturn('');
        $this->asset->shouldReceive('isPublic')->andReturn(true);

        File::shouldReceive('exists')->andReturn(true);
    }
}
