<?php

namespace BoomCMS\Tests\Integration\Asset;

class AssetEmbedTest extends AssetTest
{
    protected $url = 'http://localhost/asset/1/embed';

    protected function setType($type)
    {
        $this->asset->shouldReceive('getType')->andReturn($type);
    }

    protected function checkResponse($expected)
    {
        $this->assetIsAccessible();

        $title = 'test asset';
        $this->asset->shouldReceive('getTitle')->andReturn($title);

        $this->visit($this->url);

        $expected = str_replace('{title}', $title, $expected);
        $this->see($expected);
    }

    public function testEmbedImage()
    {
        $this->setType('image');
        $this->checkResponse('<img src="http://localhost/asset/1" alt="{title}">');
    }

    public function testEmbedVideo()
    {
        $this->setType('video');
        $this->checkResponse('<video controls poster="http://localhost/asset/1/thumb" src="http://localhost/asset/1"></video>');
    }

    public function testEmbedDocument()
    {
        $this->setType('document');
        $this->checkResponse('<a class="download" href="http://localhost/asset/1">{title}</a>');
    }

    public function testEmbedAudio()
    {
        $this->setType('audio');

        $this->asset->shouldReceive('getMimetype')->once()->andReturn('audio/mpeg');

        $this->checkResponse('<audio controls><source src="http://localhost/asset/1" type="audio/mpeg"></source></audio>');
    }
}
