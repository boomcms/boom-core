<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Core\Chunk\Timestamp;
use BoomCMS\Foundation\Chunk\AcceptsHtmlString;
use BoomCMS\Tests\AbstractTestCase;

class ChunkTimestampTest extends AbstractTestCase
{
    public function testHtmlCanBeSet()
    {
        $traits = class_uses(Timestamp::class);

        $this->assertTrue(in_array(AcceptsHtmlString::class, $traits));
    }
}
