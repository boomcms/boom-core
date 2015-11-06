<?php

namespace BoomCMS\Tests\Asset;

use BoomCMS\Core\Asset\PdfThumbnail;
use BoomCMS\Exceptions\InvalidAssetException;

class PdfThumbnailTest extends AssetTest
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testAssetMustBeAPdf()
    {
        $asset = $this->getAsset([], ['getExtension']);
        $asset
            ->expects($this->once())
            ->method('getExtension')
            ->willReturn('txt');

        new PdfThumbnail($asset);
    }

    public function testGetFilename()
    {
        $asset = $this->getAsset([], ['getFilename', 'getExtension']);
        $asset
            ->expects($this->once())
            ->method('getFilename')
            ->willReturn('test');

        $asset
            ->expects($this->once())
            ->method('getExtension')
            ->willReturn('pdf');

        $thumbnail = new PdfThumbnail($asset);

        $this->assertEquals('test.thumb', $thumbnail->getFilename());
    }
}