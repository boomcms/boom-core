<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Chunk\Timestamp;
use BoomCMS\Foundation\Chunk\AcceptsHtmlString;
use BoomCMS\Tests\AbstractTestCase;
use DateTime;

class ChunkTimestampTest extends AbstractTestCase
{
    public function testHtmlCanBeSet()
    {
        $traits = class_uses(Timestamp::class);

        $this->assertTrue(in_array(AcceptsHtmlString::class, $traits));
    }

    public function testClosureIsApplied()
    {
        $timestamp = time();
        $attrs = ['timestamp' => $timestamp];
        $text = 'Some test text';

        $closure = function(Timestamp $chunk) use($timestamp, $text) {
            if ($chunk->getTimestamp() !== $timestamp) {
                $this->fail();
            }

            return $text;
        };

        $chunk = new Timestamp($this->validPage(), $attrs, 'test');

        $this->assertEquals($chunk, $chunk->apply($closure));
        $this->assertEquals('<p>'.$text.'</p>', $chunk->setHtml('<p>{time}</p>')->render());
    }

    public function testGetDateTime()
    {
        $time = time();
        $attrs = ['timestamp' => $time];
        $chunk = new Timestamp($this->validPage(), $attrs, 'test');

        $datetime = $chunk->getDateTime();

        $this->assertInstanceOf(DateTime::class, $datetime);
        $this->assertEquals($time, $datetime->getTimestamp());
    }
}
