<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\FileInfo\Drivers\Jpg;
use Mockery as m;

class JpgTest extends BaseDriverTest
{
    public function testGetAspectRatio()
    {
        $info = $this->getInfo('test.jpg');

        $this->assertEquals(1, $info->getAspectRatio());
    }

    public function testGetMetadataReturnsArrayOfMetadata()
    {
        $info = $this->getInfo('test.jpg');

        $exif = [
            'Components Configuration' => '1, 2, 3, 0',
            'Date Time Digitized'      => '2017:02:16 17:00:00',
            'Exif Offset'              => '50',
            'Exif Version'             => '48, 50, 51, 48',
            'Flash Pix Version'        => '48, 49, 48, 48',
            'Resolution Unit'          => '2',
            'Y Cb Cr Positioning'      => '1',
        ];

        $this->assertEquals($exif, $info->getMetadata());
    }

    public function testGetCreatedAt()
    {
        $time = '2017:02:16 17:00:00';
        $keys = ['DateTimeOriginal', 'DateTimeDigitized', 'Date Time Digitized'];

        foreach ($keys as $key) {
            $jpg = m::mock(Jpg::class)->makePartial();

            $jpg
                ->shouldReceive('getMetadata')
                ->once()
                ->andReturn([
                    $key => $time,
                ]);

            $this->assertEquals($time, $jpg->getCreatedAt());
        }
    }

    public function testGetHeight()
    {
        $info = $this->getInfo('test.jpg');

        $this->assertEquals(1, $info->getHeight());
    }

    public function testGetTitle()
    {
        $info = $this->getInfo('test.jpg');

        $this->assertEquals('', $info->getTitle());
    }

    public function testGetWidth()
    {
        $info = $this->getInfo('test.jpg');

        $this->assertEquals(1, $info->getWidth());
    }
}
