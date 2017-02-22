<?php

namespace BoomCMS\Tests\Integration\Asset;

use Illuminate\Support\Facades\Response;

class AssetDownloadTest extends AssetTest
{
    protected $url = '/asset/1/download';

    public function testDownloadRequest()
    {
        $filename = 'test';
        $originalFilename = 'test-original';

        $this->asset->shouldReceive('getFilename')->once()->andReturn($filename);
        $this->asset->shouldReceive('getOriginalFilename')->once()->andReturn($originalFilename);
        
        Response::shouldReceive('download')
            ->once()
            ->with($filename, $originalFilename);

        $this->withoutMiddleware();

        $response = $this->call('GET', $this->url);
        dd($repsonse);
    }
}