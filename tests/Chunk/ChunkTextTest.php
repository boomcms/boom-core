<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Core\Chunk\Text;
use BoomCMS\Foundation\Chunk\AcceptsHtmlString;
use BoomCMS\Tests\AbstractTestCase;

class ChunkTextTest extends AbstractTestCase
{
    public function testHtmlCanBeSet()
    {
        $traits = class_uses(Text::class);

        $this->assertTrue(in_array(AcceptsHtmlString::class, $traits));
    }
}
