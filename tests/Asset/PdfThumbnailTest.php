<?php

namespace BoomCMS\Tests\Asset;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Core\Asset\PdfThumbnail;
use BoomCMS\Tests\AbstractTestCase;

class PdfThumbnailTest extends AbstractTestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testAssetMustBeAPdf()
    {
        $asset = $this->getAsset();
        $asset
            ->expects($this->once())
            ->method('getExtension')
            ->willReturn('txt');

        new PdfThumbnail($asset);
    }

    public function testGetFilename()
    {
        $asset = $this->getAsset();
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

    protected function getAsset($methods = null)
    {
        return $this->getMock(AssetInterface::class);
    }
}
