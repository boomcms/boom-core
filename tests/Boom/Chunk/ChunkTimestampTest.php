<?php

use BoomCMS\Core\Chunk\Timestamp;
use BoomCMS\Foundation\Chunk\AcceptsHtmlString;

class ChunkTimestampTest extends TestCase
{
    public function testHtmlCanBeSet()
    {
        $traits = class_uses(Timestamp::class);

        $this->assertTrue(in_array(AcceptsHtmlString::class, $traits));
    }
}
