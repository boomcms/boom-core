<?php

namespace BoomCMS\Tests\Http\Controllers\Asset;

use BoomCMS\Database\Models\Album;
use BoomCMS\Http\Controllers\Asset\AlbumAssetsController as Controller;
use BoomCMS\Tests\Http\Controllers\BaseControllerTest;
use Illuminate\Http\Request;
use Mockery as m;

class AlbumAssetsControllerTest extends BaseControllerTest
{
    protected $className = Controller::class;

    /**
     * @var Album
     */
    protected $album;

    public function setUp()
    {
        parent::setUp();

        $this->album = m::mock(Album::class);
    }

    public function testDestroy()
    {
        $assetIds = [1, 2, 3];
        $request = new Request(['assets' => $assetIds]);

        $this->album
            ->shouldReceive('removeAssets')
            ->once()
            ->with($assetIds)
            ->andReturnSelf();

        $this->assertEquals($this->album, $this->controller->destroy($request, $this->album));
    }

    public function testDestroyWithNoAssetsGiven()
    {
        $request = new Request();

        $this->album
            ->shouldReceive('removeAssets')
            ->never();

        $this->assertEquals($this->album, $this->controller->destroy($request, $this->album));
    }

    public function testStore()
    {
        $assetIds = [1, 2, 3];
        $request = new Request(['assets' => $assetIds]);

        $this->album
            ->shouldReceive('addAssets')
            ->once()
            ->with($assetIds)
            ->andReturnSelf();

        $this->assertEquals($this->album, $this->controller->store($request, $this->album));
    }

    public function testStoreWithNoAssetsGiven()
    {
        $request = new Request();

        $this->album
            ->shouldReceive('addAssets')
            ->never();

        $this->assertEquals($this->album, $this->controller->destroy($request, $this->album));
    }
}
