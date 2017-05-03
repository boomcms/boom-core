<?php

namespace BoomCMS\Tests\Chunk\Slideshow;

use BoomCMS\Chunk\Slideshow\Slide;
use BoomCMS\Tests\AbstractTestCase;

class SlideTest extends AbstractTestCase
{
    public function testGetAssetId()
    {
        $assetId = 1;
        $slide = new Slide(['asset_id' => $assetId]);

        $this->assertEquals($assetId, $slide->getAssetId());
    }
}
