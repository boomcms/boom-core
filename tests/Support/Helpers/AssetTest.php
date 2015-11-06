<?php

namespace BoomCMS\Tests\Support\Helpers;

use BoomCMS\Core\Asset\Asset;
use BoomCMS\Support\Helpers\Asset as AssetHelper;
use BoomCMS\Tests\AbstractTestCase;

class AssetTest extends AbstractTestCase
{
    protected $types;

    public function setUp()
    {
        parent::setUp();

        $config = include(__DIR__.'/../../../src/config/boomcms/assets.php');
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
        $namespace = 'BoomCMS\Http\Controllers\Asset\\';

        $asset = new Asset();
        $this->assertEquals(null, AssetHelper::controller($asset));

        $pdf = new Asset(['id' => 1, 'extension' => 'pdf']);
        $this->assertEquals($namespace.'Pdf', AssetHelper::controller($pdf));

        $image = new Asset(['id' => 1, 'type' => 'image']);
        $this->assertEquals($namespace.'Image', AssetHelper::controller($image));

        $tiff = new Asset(['id' => 1, 'type' => 'image', 'extension' => 'tiff']);
        $this->assertEquals($namespace.'Tiff', AssetHelper::controller($tiff));

        $video = new Asset(['id' => 1, 'type' => 'video']);
        $this->assertEquals($namespace.'Video', AssetHelper::controller($video));
    }
}
