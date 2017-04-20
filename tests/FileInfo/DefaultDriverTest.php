<?php

namespace BoomCMS\Tests\FileInfo;

class DefaultDriverTest extends BaseDriverTest
{
    /**
     * @var FileInfoDriver
     */
    protected $info;

    public function setUp()
    {
        parent::setUp();

        $this->info = $this->getInfo('test.ods');
    }

    public function testGetAspectRatio()
    {
        $this->assertEquals(0, $this->info->getAspectRatio());
    }

    public function testGetMetadataReturnsArray()
    {
        $this->assertEquals([], $this->info->getMetadata());
    }

    public function testGetCreatedAt()
    {
        $this->assertNull($this->info->getCreatedAt());
    }

    public function testGetHeight()
    {
        $this->assertEquals(0, $this->info->getHeight());
    }

    public function testGetTitle()
    {
        $this->assertEquals('', $this->info->getTitle());
    }

    public function testGetWidth()
    {
        $this->assertEquals(0, $this->info->getWidth());
    }
}
