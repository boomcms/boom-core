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

    public function testGetAssetType()
    {
        $this->assertEquals('doc', $this->info->getAssetType());
    }

    public function testGetCreatedAt()
    {
        $this->assertNull($this->info->getCreatedAt());
    }

    public function testGetExtension()
    {
        $this->markTestIncomplete();
    }

    public function testGetExtensionReturnsExtensionOfOriginalFilename()
    {
        $this->markTestIncomplete();
    }

    public function testGetExtensionGuessExtension()
    {
        $this->markTestIncomplete();
    }

    public function testGetFilename()
    {
        $this->markTestIncomplete();
    }

    public function testGetFilenameReturnsOriginalFilename()
    {
        $this->markTestIncomplete();
    }

    public function testGetFilesize()
    {
        $this->markTestIncomplete();
    }

    public function testGetHeight()
    {
        $this->assertEquals(0, $this->info->getHeight());
    }

    public function testGetMetadataReturnsArray()
    {
        $this->assertEquals([], $this->info->getMetadata());
    }

    public function testGetMimetype()
    {
        $this->markTestIncomplete();
    }

    public function testGetPath()
    {
        $this->markTestIncomplete();
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
