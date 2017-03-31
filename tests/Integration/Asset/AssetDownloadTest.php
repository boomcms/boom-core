<?php

namespace BoomCMS\Tests\Integration\Asset;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use BoomCMS\Support\Facades\Asset;

class AssetDownloadTest extends AssetTest
{
    protected $url = 'http://localhost/asset/1/download';

    public function testDownloadRequest()
    {
        $file = file_get_contents(realpath(__DIR__.'/../../files/test.jpg'));
        $originalFilename = 'test-original';

        $this->asset->shouldReceive('getOriginalFilename')->andReturn($originalFilename);

        $this->assetIsAccessible();

        Asset::shouldReceive('file')
            ->once()
            ->with($this->asset)
            ->andReturn($file);

        // Prevent the download from being logged.
        Auth::shouldReceive('check')->andReturn(true);

        $response = $this->call('GET', $this->url);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($file, $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('download; filename="'.$originalFilename.'"', $this->response->headers->get('content-disposition'));
    }
}
