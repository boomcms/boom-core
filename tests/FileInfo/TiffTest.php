<?php

namespace BoomCMS\Tests\FileInfo;

use Carbon\Carbon;

class TiffTest extends JpgTest
{
    protected $filename = 'test.tiff';

    /**
     * The value of the date:create field of our test image should be returned.
     */
    public function testGetCreatedAt()
    {
        $info = $this->getInfo($this->filename);
        $date = Carbon::parse('2017-02-17T10:46:43+00:00');

        $this->assertEquals($date, $info->getCreatedAt());
    }

    public function testGetMetadataReturnsArrayOfMetadata()
    {
        $info = $this->getInfo($this->filename);

        $exif = [
            'date:create' => '2017-02-17T10:46:43+00:00',
            'date:modify' => '2017-02-17T10:46:43+00:00',
            'tiff:alpha' => 'unspecified',
            'tiff:endian' => 'lsb',
            'tiff:photometric' => 'RGB',
            'tiff:rows-per-strip' => '64',
        ];

        $this->assertArraySubset($exif, $info->getMetadata());
    }
}
