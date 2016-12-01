<?php

namespace BoomCMS\Tests\Support\Helpers;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Support\Helpers\Asset as AssetHelper;
use BoomCMS\Tests\AbstractTestCase;

class AssetTest extends AbstractTestCase
{
    protected $types;

    public function setUp()
    {
        parent::setUp();

        $config = include __DIR__.'/../../../src/config/boomcms/assets.php';
        $this->types = $config['assets']['types'];
    }

    public function testTypes()
    {
        $this->AssertEquals($this->types, AssetHelper::types());
    }

    public function testTypeFromMimetype()
    {
        foreach ($this->types as $type => $mimetypes) {
            foreach ($mimetypes as $mimetype) {
                $this->assertEquals($type, AssetHelper::typeFromMimetype($mimetype));
            }
        }
    }

    public function testController()
    {
        $namespace = 'BoomCMS\Http\Controllers\ViewAsset\\';

        $asset = $this->createMock(Asset::class, ['getExtension']);
        $asset->expects($this->any())->method('getExtension')->will($this->returnValue(null));

        $this->assertEquals(null, AssetHelper::controller($asset));

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

        $video = $this->createMock(Asset::class, ['getType', 'getExtension']);
        $video->expects($this->any())->method('getExtension')->will($this->returnValue('mp4'));
        $video->expects($this->any())->method('getType')->will($this->returnValue('video'));
        $this->assertEquals($namespace.'Video', AssetHelper::controller($video));
    }
}
