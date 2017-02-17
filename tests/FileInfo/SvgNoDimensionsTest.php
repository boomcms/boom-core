<?php

namespace BoomCMS\Tests\FileInfo;

class SvgNoDimensionsTest extends SvgTest
{
    /**
     * @var FileInfoDriver
     */
    protected $info;

    public function setUp()
    {
        parent::setUp();

        $this->info = $this->getInfo('test-no-dimensions.svg');
    }

    public function testGetAspectRatio()
    {
        $this->assertEquals(0, $this->info->getAspectRatio());
    }

    public function testGetHeight()
    {
        $this->assertEquals(0, $this->info->getHeight());
    }

    public function testGetWidth()
    {
        $this->assertEquals(0, $this->info->getWidth());
    }
}
