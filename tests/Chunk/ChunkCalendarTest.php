<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Chunk\Calendar;
use BoomCMS\Database\Models\Chunk\Calendar as Model;
use BoomCMS\Database\Models\Page;
use BoomCMS\Tests\AbstractTestCase;

class ChunkCalendarTest extends AbstractTestCase
{
    public function testGetDates()
    {
        $noContent = ['', null, []];

        foreach ($noContent as $initial) {
            $chunk = new Calendar(new Page(), [Model::ATTR_CONTENT => $initial], 'test');
            $this->assertEquals([], $chunk->getDates());
        }

        $content = ['date' => 'text'];
        $chunk = new Calendar(new Page(), [Model::ATTR_CONTENT => $content], 'test');
        $this->assertEquals($content, $chunk->getDates());
    }

    public function testHasContent()
    {
        $noContent = ['', null, []];

        foreach ($noContent as $content) {
            $chunk = new Calendar(new Page(), [Model::ATTR_CONTENT => $content], 'test');
            $this->assertFalse($chunk->hasContent());
        }

        $chunk = new Calendar(new Page(), [Model::ATTR_CONTENT => ['date' => 'text']], 'test');
        $this->assertTrue($chunk->hasContent());
    }
}
