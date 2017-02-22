<?php

namespace BoomCMS\Tests\FileInfo;

use BoomCMS\FileInfo\Drivers\Word;
use Carbon\Carbon;
use Mockery as m;

class DocxTest extends BaseDriverTest
{
    /**
     * @var FileInfoDriver
     */
    protected $info;

    public function setUp()
    {
        parent::setUp();

        $this->info = $this->getInfo('test.docx');
    }

    /**
     * Docx don't have an aspect ratio - 0 should be returned.
     */
    public function testGetAspectRatio()
    {
        $this->assertEquals(0, $this->info->getAspectRatio());
    }

    public function testGetMetadataReturnsArray()
    {
        $metadata = [
            'created' => 1487329635,
        ];

        $this->assertArraySubset($metadata, $this->info->getMetadata());
    }

    public function testGetCreatedAt()
    {
        $date = Carbon::createFromTimestamp(1487329635);

        $this->assertEquals($date, $this->info->getCreatedAt());
    }

    /**
     * If the date is invalid null should be returned.
     */
    public function testGetCreatedAtWithInvalidDate()
    {
        $info = m::mock(Word::class)->makePartial();

        $info
            ->shouldReceive('getMetadata')
            ->once()
            ->andReturn([
                'created' => 'rubbish date',
            ]);

        $this->assertNull($info->getCreatedAt());
    }

    /**
     * PDFs don't have a height - 0 should be returned.
     */
    public function testGetHeight()
    {
        $this->assertEquals(0, $this->info->getHeight());
    }

    public function testGetTitle()
    {
        $this->assertEquals('', $this->info->getTitle());
    }

    /**
     * PDFs don't have a width - 0 should be returned.
     */
    public function testGetWidth()
    {
        $this->assertEquals(0, $this->info->getWidth());
    }
}
