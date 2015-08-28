<?php

use BoomCMS\Core\Chunk\Text;
use BoomCMS\Foundation\Chunk\AcceptsHtmlString;

class ChunkTextTest extends TestCase
{
    public function testHtmlCanBeSet()
    {
        $traits = class_uses(Text::class);

        $this->assertTrue(in_array(AcceptsHtmlString::class, $traits));
    }
}
