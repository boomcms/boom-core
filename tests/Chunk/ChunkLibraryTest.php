<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Core\Chunk\Library;
use BoomCMS\Core\Page\Page;
use BoomCMS\Tests\AbstractTestCase;

class ChunkLibraryTest extends AbstractTestCase
{
    public function testDoesntSetAnyAttributes()
    {
        $chunk = $this->getChunk();

        $this->assertEquals([], $chunk->attributes());
    }

    /**
     * getParams() should return the params array
     */
    public function testGetParams()
    {
        $params = ['params' => ['tag' => 'test']];

        $chunk = $this->getChunk($params);
        $this->assertEquals($params['params'], $chunk->getParams());
    }

    /**
     * If no params attribute is given getParams() should return an empty array
     */
    public function testGetParamsAlwaysReturnsArray()
    {
        $values = [
            [],
            ['params' => null],
            ['params' => []],
        ];

        foreach ($values as $v) {
            $chunk = $this->getChunk($v);
            $this->assertEquals([], $chunk->getParams());
        }
    }

    /**
     * getTag() should return the tag from the params array
     */
    public function testGetTagReturnsTag()
    {
        $tag ='test';

        $chunk = $this->getChunk(['params' => ['tag' => $tag]]);
        $this->assertEquals($tag, $chunk->getTag());
    }

    public function testGetTagReturnsNull()
    {
        $values = [
            [],
            ['tag' => ''],
            ['tag' => null],
        ];

        foreach ($values as $v) {
            $chunk = $this->getChunk($v);
            $this->assertNull($chunk->getTag());
        }
    }

    public function testHasContentIsFalse()
    {
        // Possible values of the params attribute which should be counted as the chunk having no content.
        $values = [
            [],
            null,
            ['tag' => ''],
            ['anykey' => null],
            ['anykey' => ''],
            ['limit' => 0],
        ];

        foreach ($values as $v) {
            $chunk = $this->getChunk(['params' => $v]);

            $this->assertFalse($chunk->hasContent());
        }
    }

    public function testHasContentIfParamsIsNotEmptyArray()
    {
        // Possible values of the params attribute which should be counted as the chunk having content.
        $values = [
            ['tag' => 'test'],
            ['type' => 'image'],
            ['limit' => 10],
            ['tag' => 'test', 'type' => null],
        ];

        foreach ($values as $v) {
            $chunk = $this->getChunk(['params' => $v]);

            $this->assertTrue($chunk->hasContent());
        }
    }

    protected function getChunk($attrs = [], $slotname = 'test', $editable = true)
    {
        return new Library(new Page([]), $attrs, $slotname, $editable);
    }
}
