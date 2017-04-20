<?php

namespace BoomCMS\Tests\FileInfo;

use BoomCMS\FileInfo\Drivers\Image;
use Carbon\Carbon;
use Mockery as m;

class ImageTest extends BaseDriverTest
{
    /**
     * @var Image
     */
    protected $image;

    public function setUp()
    {
        parent::setUp();

        $this->image = m::mock(Image::class)->makePartial();
    }

    public function testGetCreatedAtCanComeFromDifferentFields()
    {
        $timestamp = '2017-02-16T16:56:18+00:00';
        $time = Carbon::parse('2017-02-16T16:56:18+00:00');
        $keys = ['date:create', 'exif:DateTimeOriginal', 'exif:DateTimeDigitized', 'exif:DateTime'];

        foreach ($keys as $key) {
            $this->image
                ->shouldReceive('getMetadata')
                ->once()
                ->andReturn([
                    $key => $timestamp,
                ]);

            $this->assertEquals($time, $this->image->getCreatedAt());
        }
    }

    /**
     * If the date is invalid null should be returned.
     */
    public function testGetCreatedAtWithInvalidDate()
    {
        $this->image
            ->shouldReceive('getMetadata')
            ->once()
            ->andReturn([
                'exif:DateTimeOriginal' => '2016:06:26 13:59:',
            ]);

        $this->assertNull($this->image->getCreatedAt());
    }
}
