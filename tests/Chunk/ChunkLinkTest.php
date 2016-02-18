<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Chunk\Link;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class ChunkLinkTest extends AbstractTestCase
{
    /**
     * @var Text
     */
    protected $chunk;

    public function setUp()
    {
        parent::setUp();

        $this->chunk = m::mock(Link::class)->makePartial();
    }

    public function testEditableTextByDefault()
    {
        $this->assertArraySubset(['data-boom-edittext' => 1], $this->chunk->attributes());
    }

    public function testNoTextDisablesEditableText()
    {
        $this->chunk->noText();

        $this->assertArraySubset(['data-boom-edittext' => 0], $this->chunk->attributes());
    }
}
