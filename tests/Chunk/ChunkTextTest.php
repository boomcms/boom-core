<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Chunk\Text;
use BoomCMS\Database\Models\Page;
use BoomCMS\Foundation\Chunk\AcceptsHtmlString;
use BoomCMS\Tests\AbstractTestCase;

class ChunkTextTest extends AbstractTestCase
{
    /**
     * @var Page
     */
    protected $page;

    public function setUp()
    {
        parent::setUp();

        $this->page = new Page();
    }

    public function testHasContentReturnsFalseWithNoContent()
    {
        $content = [
            null,
            '',
            ' ',
        ];
    
        foreach ($content as $text) {
            $chunk = new Text($this->page, ['text' => $text], 'test', false);

            $this->assertFalse($chunk->hasContent());
        }
    }

    public function testHtmlCanBeSet()
    {
        $traits = class_uses(Text::class);

        $this->assertTrue(in_array(AcceptsHtmlString::class, $traits));
    }
}
