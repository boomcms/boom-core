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
        $this->assertNotEmpty($this->info->getMetadata());
    }

    public function testGetCreatedAt()
    {
        $metadata = $this->info->getMetadata();

        $date = Carbon::parse($metadata['date:create']);

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
