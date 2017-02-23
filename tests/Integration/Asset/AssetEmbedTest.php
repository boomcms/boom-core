<?php

namespace BoomCMS\Tests\Integration\Asset;

class AssetEmbedTest extends AssetTest
{
    protected $url = 'http://localhost/asset/1/embed';

    public function testEmbedImage()
    {
        $this->assetIsAccessible();

        $title = 'test asset';
        $this->asset->shouldReceive('getType')->andReturn('image');
        $this->asset->shouldReceive('getTitle')->andReturn($title);

        $this->visit($this->url);
        $this->see('<img src="http://localhost/asset/1" alt="'.$title.'">');
    }

    public function testEmbedVideo()
    {
        $this->assetIsAccessible();

        $title = 'test asset';
        $this->asset->shouldReceive('getType')->andReturn('video');
        $this->asset->shouldReceive('getTitle')->andReturn($title);

        $this->visit($this->url);
        $this->see('<video poster="http://localhost/asset/1/thumb" src="http://localhost/asset/1"></video>');
    }
}