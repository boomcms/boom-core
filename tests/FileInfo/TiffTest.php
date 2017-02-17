<?php

namespace BoomCMS\Tests\FileInfo;

use Carbon\Carbon;

class TiffTest extends JpgTest
{
    protected $filename = 'test.tiff';

    public function testGetDescription()
    {
        $info = $this->getInfo($this->filename);

        $this->assertEquals('', $info->getDescription());
    }

    public function testGetCopyright()
    {
        $info = $this->getInfo($this->filename);

        $this->assertEquals('', $info->getCopyright());
    }

    /**
     * The value of the date:create field of our test image should be returned.
     */
    public function testGetCreatedAt()
    {
        $info = $this->getInfo($this->filename);
        $metadata = $info->getMetadata();

        $date = Carbon::parse($metadata['date:create']);

        $this->assertEquals($date, $info->getCreatedAt());
    }

    public function testGetMetadataReturnsArrayOfMetadata()
    {
        $info = $this->getInfo($this->filename);

        $exif = [
            'tiff:endian'         => 'lsb',
            'tiff:photometric'    => 'RGB',
            'tiff:rows-per-strip' => '64',
        ];

        $this->assertArraySubset($exif, $info->getMetadata());
    }
}
