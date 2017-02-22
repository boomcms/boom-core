<?php

namespace BoomCMS\Tests\FileInfo;

use BoomCMS\FileInfo\Drivers\Pdf;
use Carbon\Carbon;
use Mockery as m;

class PdfTest extends BaseDriverTest
{
    /**
     * @var FileInfoDriver
     */
    protected $info;

    public function setUp()
    {
        parent::setUp();

        $this->info = $this->getInfo('test.pdf');
    }

    /**
     * PDF aspect ratios can't be determined, so 0 should be returned.
     */
    public function testGetAspectRatio()
    {
        $this->assertEquals(0, $this->info->getAspectRatio());
    }

    public function testGetMetadataReturnsArray()
    {
        $metadata = [
            'CreationDate' => '2017-02-17T11:07:56+00:00',
            'Creator'      => 'Writer',
            'Pages'        => 1,
            'Producer'     => 'LibreOffice 5.1',
        ];

        $this->assertEquals($metadata, $this->info->getMetadata());
    }

    public function testGetCreatedAt()
    {
        $date = Carbon::parse('2017-02-17T11:07:56+00:00');

        $this->assertEquals($date, $this->info->getCreatedAt());
    }

    /**
     * If the date is invalid null should be returned.
     */
    public function testGetCreatedAtWithInvalidDate()
    {
        $info = m::mock(Pdf::class)->makePartial();

        $info
            ->shouldReceive('getMetadata')
            ->once()
            ->andReturn([
                'CreationDate' => 'rubbish date',
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
