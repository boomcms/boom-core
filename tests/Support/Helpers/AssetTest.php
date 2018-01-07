<?php

namespace BoomCMS\Tests\Support\Helpers;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Support\Helpers\Asset as AssetHelper;
use BoomCMS\Tests\AbstractTestCase;

class AssetTest extends AbstractTestCase
{
    public function testController()
    {
        $namespace = 'BoomCMS\Http\Controllers\ViewAsset\\';

        $asset = $this->createMock(Asset::class, ['getExtension']);
        $asset->expects($this->any())->method('getExtension')->will($this->returnValue(''));

        $this->assertEquals('', AssetHelper::controller($asset));

        $pdf = $this->createMock(Asset::class, ['getExtension']);
        $pdf->expects($this->any())->method('getExtension')->will($this->returnValue('pdf'));

        $this->assertEquals($namespace.'Pdf', AssetHelper::controller($pdf));

        $image = $this->createMock(Asset::class, ['getType', 'getExtension']);
        $image->expects($this->any())->method('getExtension')->will($this->returnValue('png'));
        $image->expects($this->any())->method('getType')->will($this->returnValue('image'));
        $this->assertEquals($namespace.'Image', AssetHelper::controller($image));

        $tiff = $this->createMock(Asset::class, ['getType', 'getExtension']);
        $tiff->expects($this->any())->method('getType')->will($this->returnValue('image'));
        $tiff->expects($this->any())->method('getExtension')->will($this->returnValue('tiff'));
        $this->assertEquals($namespace.'Tiff', AssetHelper::controller($tiff));
    }
}
