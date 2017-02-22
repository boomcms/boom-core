<?php

namespace BoomCMS\Tests\Integration\Asset;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\File;
use Mockery as m;

class AssetTest extends AbstractTestCase
{
    protected $asset;

    protected $url = '/asset/1';

    public function setUp()
    {
        $this->asset = m::mock(Asset::class);

        AssetFacade::shouldReceive('find')->andReturn($this->asset);
    }

    public function test404IfAssetFileNotFound()
    {
        $filename = 'test';

        $this->asset->shouldReceive('getFilename')->once()->andReturn($filename);
        File::shouldReceive('exists')->once()->with($filename)->andReturn(false);

        $this->visit($this->url);

        $this->assertResponseStatus(404);
    }

    public function test401IfAssetNotPublicAndUserIsGuest()
    {
        $this->markTestIncomplete();
    }
}