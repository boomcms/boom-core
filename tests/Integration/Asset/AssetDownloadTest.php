<?php

namespace BoomCMS\Tests\Integration\Asset;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetDownloadTest extends AssetTest
{
    protected $url = 'http://localhost/asset/1/download';

    public function testDownloadRequest()
    {
        $filename = realpath(__DIR__.'/../../files/test.jpg');
        $originalFilename = 'test-original';

        $this->asset->shouldReceive('isPublic')->andReturn(true);
        $this->asset->shouldReceive('getFilename')->andReturn($filename);
        $this->asset->shouldReceive('getOriginalFilename')->andReturn($originalFilename);

        // Prevent the download from being logged.
        Auth::shouldReceive('check')->andReturn(true);

        $response = $this->call('GET', $this->url);

        $this->assertInstanceOf(BinaryFileResponse::class, $response);
        $this->assertEquals($filename, $response->getFile());
    }
}