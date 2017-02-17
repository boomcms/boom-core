<?php

namespace BoomCMS\Tests\FileInfo;

use BoomCMS\FileInfo\Drivers\Image;
use Carbon\Carbon;
use Mockery as m;

class JpgTest extends BaseDriverTest
{
    protected $filename = 'test.jpg';

    public function testGetAspectRatio()
    {
        $info = $this->getInfo($this->filename);

        $this->assertEquals(1, $info->getAspectRatio());
    }

    public function testGetMetadataReturnsArrayOfMetadata()
    {
        $info = $this->getInfo($this->filename);

        $exif = [
            "date:create" => "2017-02-16T16:56:18+00:00",
            "date:modify" => "2017-02-16T16:56:09+00:00",
            "exif:ComponentsConfiguration" => "1, 2, 3, 0",
            "exif:DateTimeDigitized" => "2017:02:16 17:00:00",
            "exif:ExifOffset" => "50",
            "exif:ExifVersion" => "48, 50, 51, 48",
            "exif:FlashPixVersion" => "48, 49, 48, 48",
            "exif:ResolutionUnit" => "2",
            "exif:YCbCrPositioning" => "1",
            "jpeg:colorspace" => "2",
            "jpeg:sampling-factor" => "1x1,1x1,1x1",
        ];

        $this->assertEquals($exif, $info->getMetadata());
    }

    /**
     * The value of the date:create field of our test image should be returned.
     */
    public function testGetCreatedAt()
    {
        $info = $this->getInfo($this->filename);
        $date = Carbon::parse('2017-02-16T16:56:18+00:00');

        $this->assertEquals($date, $info->getCreatedAt());
    }

    public function testGetCreatedAtCanComeFromDifferentFields()
    {
        $timestamp = '2017-02-16T16:56:18+00:00';
        $time = Carbon::parse('2017-02-16T16:56:18+00:00');
        $keys = ['date:create', 'exif:DateTimeOriginal', 'exif:DateTimeDigitized'];

        foreach ($keys as $key) {
            $jpg = m::mock(Image::class)->makePartial();

            $jpg
                ->shouldReceive('getMetadata')
                ->once()
                ->andReturn([
                    $key => $timestamp,
                ]);

            $this->assertEquals($time, $jpg->getCreatedAt());
        }
    }

    public function testGetHeight()
    {
        $info = $this->getInfo($this->filename);

        $this->assertEquals(1, $info->getHeight());
    }

    public function testGetTitle()
    {
        $info = $this->getInfo($this->filename);

        $this->assertEquals('', $info->getTitle());
    }

    public function testGetWidth()
    {
        $info = $this->getInfo($this->filename);

        $this->assertEquals(1, $info->getWidth());
    }
}
