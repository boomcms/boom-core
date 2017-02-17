<?php

namespace BoomCMS\Tests\FileInfo;

use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use Carbon\Carbon;

class PngTest extends BaseDriverTest
{
    /**
     * @var FileInfoDriver
     */
    protected $info;

    public function setUp()
    {
        parent::setUp();

        $this->info = $this->getInfo('test.png');
    }

    public function testGetAspectRatio()
    {
        $this->assertEquals(2, $this->info->getAspectRatio());
    }

    public function testGetMetadataReturnsArray()
    {
        $metadata = [
            'date:create' => '2017-02-17T10:34:28+00:00',
            'date:modify' => '2017-02-17T10:34:28+00:00',
            'png:IHDR.bit-depth-orig' => '8',
            'png:IHDR.bit_depth' => '8',
            'png:IHDR.color-type-orig' => '2',
            'png:IHDR.color_type' => '2 (Truecolor)',
            'png:IHDR.interlace_method' => '0 (Not interlaced)',
            'png:IHDR.width,height' => '2, 1',
            'png:pHYs' => 'x_res=2835, y_res=2835, units=1',
            'png:sRGB' => 'intent=0 (Perceptual Intent)',
            'png:tIME' => '2017-02-17T10:34:28Z',
        ];

        $this->assertEquals($metadata, $this->info->getMetadata());
    }

    public function testGetCreatedAt()
    {
        $date = Carbon::parse('2017-02-17T10:34:28+00:00');

        $this->assertEquals($date, $this->info->getCreatedAt());
    }

    public function testGetHeight()
    {
        $this->assertEquals(1, $this->info->getHeight());
    }

    public function testGetTitle()
    {
        $this->assertEquals('', $this->info->getTitle());
    }

    public function testGetWidth()
    {
        $this->assertEquals(2, $this->info->getWidth());
    }
}
