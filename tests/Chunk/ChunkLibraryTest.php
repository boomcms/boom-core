<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Chunk\Library;
use BoomCMS\Database\Models\Page;
use BoomCMS\Tests\AbstractTestCase;

class ChunkLibraryTest extends AbstractTestCase
{
    public function testDoesntSetAnyAttributes()
    {
        $chunk = $this->getChunk();

        $this->assertEquals([], $chunk->attributes());
    }

    /**
     * getParams() should return the params array.
     */
    public function testGetParams()
    {
        $params = ['params' => ['tag' => 'test']];

        $chunk = $this->getChunk($params);
        $this->assertEquals($params['params'], $chunk->getParams());
    }

    /**
     * If no params attribute is given getParams() should return an empty array.
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
     * getTag() should return the tag from the params array.
     */
    public function testGetTagsReturnsTags()
    {
        $tag = ['test'];

        $chunk = $this->getChunk(['params' => ['tag' => $tag]]);
        $this->assertEquals($tag, $chunk->getTags());
    }

    public function testGetTagReturnsEmptyArray()
    {
        $values = [
            [],
            ['tag' => ''],
            ['tag' => null],
            ['tag' => []],
        ];

        foreach ($values as $v) {
            $chunk = $this->getChunk($v);
            $this->assertEquals([], $chunk->getTags());
        }
    }

    public function getGetOrderRetunsOrder()
    {
        $order = 'last_modified desc';

        $chunk = $this->getChunk(['order' => $order]);
        $this->assertEquals($order, $chunk->getOrder());
    }

    public function getGetLimitRetunsLimit()
    {
        $limit = 100;

        $chunk = $this->getChunk(['limit' => $limit]);
        $this->assertEquals($limit, $chunk->getLimit());
    }

    public function testGetLimitReturnsNull()
    {
        $values = [
            [],
            ['limit' => ''],
            ['limit' => null],
        ];

        foreach ($values as $v) {
            $chunk = $this->getChunk($v);
            $this->assertNull($chunk->getLimit());
        }
    }

    public function testHasContentIsFalse()
    {
        // Possible values of the params attribute which should be counted as the chunk having no content.
        $values = [
            [],
            null,
            ['tag'    => ''],
            ['anykey' => null],
            ['anykey' => ''],
            ['limit'  => 0],
        ];

        foreach ($values as $v) {
            $chunk = $this->getChunk(['params' => $v]);

            $this->assertFalse($chunk->hasContent());
        }
    }

    /**
     * A library doesn't have content if the params only contain a sort order or limit.
     * 
     * There most be a filter parameter as well.
     */
    public function testHasContentIsFalseIfParamsDontContainFilters()
    {
        $chunk = $this->getChunk(['params' => ['order' => 'last_modified desc', 'limit' => 100]]);

        $this->assertFalse($chunk->hasContent());
    }

    public function testHasContentIfParamsIsNotEmptyArray()
    {
        // Possible values of the params attribute which should be counted as the chunk having content.
        $values = [
            ['tag' => 'test'],
            ['type'  => 'image'],
            ['tag'   => 'test', 'type' => null],
            ['tag' => ['test']],
        ];

        foreach ($values as $v) {
            $chunk = $this->getChunk(['params' => $v]);

            $this->assertTrue($chunk->hasContent());
        }
    }

    public function testMergeParamsThenGet()
    {
        $params = ['tag' => 'test'];
        $merged = ['limit' => 4];

        $chunk = $this->getChunk(['params' => $params]);
        $chunk->mergeParams($merged);

        $this->assertEquals($params + $merged, $chunk->getParams());
    }

    public function testMergeParamsAreOverwritten()
    {
        $params = ['limit' => 5];
        $merged = ['tag' => 'test', 'limit' => 4];

        $chunk = $this->getChunk(['params' => $params]);
        $chunk->mergeParams($merged);

        $this->assertEquals(['tag' => 'test', 'limit' => 5], $chunk->getParams());
    }

    public function testMergeParamsDoesntRemoveFilters()
    {
        $params = ['limit' => 5, 'tag' => 'test'];
        $merged = ['limit' => 4];

        $chunk = $this->getChunk(['params' => $params]);
        $chunk->mergeParams($merged);

        $this->assertEquals(['tag' => 'test', 'limit' => 5], $chunk->getParams());
    }

    public function testMergeParamsReturnsSelf()
    {
        $chunk = $this->getChunk();

        $this->assertEquals($chunk, $chunk->mergeParams([]));
    }

    protected function getChunk($attrs = [], $slotname = 'test', $editable = true)
    {
        return new Library(new Page([]), $attrs, $slotname, $editable);
    }
}
