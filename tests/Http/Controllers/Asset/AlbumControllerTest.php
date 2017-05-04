<?php

namespace BoomCMS\Tests\Http\Controllers\Asset;

use BoomCMS\Database\Models\Album;
use BoomCMS\Http\Controllers\Asset\AlbumController as Controller;
use BoomCMS\Support\Facades\Album as AlbumFacade;
use BoomCMS\Tests\Http\Controllers\BaseControllerTest;
use Illuminate\Http\Request;
use Mockery as m;

class AlbumControllerTest extends BaseControllerTest
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

        $this->requireRole('manageAlbums');
    }

    public function testDestroy()
    {
        AlbumFacade::shouldReceive('delete')
            ->once()
            ->with($this->album);

        $this->controller->destroy($this->album);
    }

    public function testUpdate()
    {
        $params = [
            Album::ATTR_NAME        => 'test album',
            Album::ATTR_DESCRIPTION => 'with a description',
        ];

        $request = new Request($params);

        $this->album
            ->shouldReceive('fill')
            ->once()
            ->with($params);

        AlbumFacade::shouldReceive('save')
            ->once()
            ->with($this->album)
            ->andReturnSelf();

        $this->assertEquals($this->album, $this->controller->update($this->album, $request));
    }

    public function testUpdateOnlyExpectedFieldsAreUpdated()
    {
        $params = [
            Album::ATTR_NAME        => 'test album',
            Album::ATTR_DESCRIPTION => 'with a description',
            Album::ATTR_ORDER       => 'value',
            Album::ATTR_ID          => 1,
            Album::ATTR_ASSET_COUNT => 1,
            Album::ATTR_SITE        => 1,
            Album::ATTR_SLUG        => 'test',
        ];

        $expected = [Album::ATTR_NAME, Album::ATTR_DESCRIPTION, Album::ATTR_ORDER];
        $request = new Request($params);

        $this->album
            ->shouldReceive('fill')
            ->once()
            ->with(array_only($params, $expected))
            ->andReturnSelf();

        AlbumFacade::shouldReceive('save')
            ->once()
            ->with($this->album)
            ->andReturnSelf();

        $this->assertEquals($this->album, $this->controller->update($this->album, $request));
    }

    public function testIndex()
    {
        $assetIds = [1, 2, 3];
        $assets = collect(new Asset(), new Asset());
        $request = new Request(['assets' => $assetIds]);

        AlbumFacade::shouldReceive('findByAssetIds')
            ->once()
            ->with($assetIds)
            ->andReturn($assets);

        $this->assertEquals($assets, $this->controller->index($request));
    }

    public function testStoreWithNameOnly()
    {
        $request = new Request(['name' => 'new album']);

        AlbumFacade::shouldReceive('create')
            ->once()
            ->with($request->input('name'), null)
            ->andReturn($this->model);

        $this->assertEquals($this->model, $this->controller->store($request));
    }

    public function testStoreWithDescriptionOnly()
    {
        $request = new Request(['description' => 'new album']);

        AlbumFacade::shouldReceive('create')
            ->once()
            ->with('Untitled', $request->input('description'))
            ->andReturn($this->model);

        $this->assertEquals($this->model, $this->controller->store($request));
    }
}
