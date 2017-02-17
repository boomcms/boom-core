<?php

namespace BoomCMS\Tests\FileInfo;

class SvgTest extends BaseDriverTest
{
    /**
     * @var FileInfoDriver
     */
    protected $info;

    public function setUp()
    {
        parent::setUp();

        $this->info = $this->getInfo('test.svg');
    }

    public function testGetAspectRatio()
    {
        $this->assertEquals(4.1615015914015681, $this->info->getAspectRatio());
    }

    /**
     * SVGs don't contain metadata so an empty array should be returned
     */
    public function testGetMetadataReturnsArray()
    {
        $this->assertEquals([], $this->info->getMetadata());
    }

    public function testGetCreatedAt()
    {
        $this->assertEquals(null, $this->info->getCreatedAt());
    }

    public function testGetHeight()
    {
        $this->assertEquals(185.057, $this->info->getHeight());
    }

    public function testGetTitle()
    {
        $this->assertEquals('', $this->info->getTitle());
    }

    public function testGetWidth()
    {
        $this->assertEquals(770.115, $this->info->getWidth());
    }
}
