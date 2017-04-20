<?php

namespace BoomCMS\Tests\Page\History\Diff;

use BoomCMS\Database\Models\PageVersion as Version;
use BoomCMS\Page\History\Diff\ChunkChange;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Lang;

class ChunkChangeTest extends AbstractTestCase
{
    protected $className = ChunkChange::class;

    protected $types = [
        'text'      => 'font',
        'linkset'   => 'link',
        'asset'     => 'paperclip',
        'library'   => 'book',
        'calendar'  => 'calendar',
        'timestamp' => 'clock-o',
        'slideshow' => 'picture-o',
        'location'  => 'globe',
        'html'      => 'code',
    ];

    public function testGetIcon()
    {
        foreach ($this->types as $chunkType => $icon) {
            $new = new Version([Version::ATTR_CHUNK_TYPE => $chunkType]);
            $old = new Version();

            $change = new ChunkChange($new, $old);

            $this->assertEquals($icon, $change->getIcon());
        }
    }

    public function testGetSummary()
    {
        foreach (array_keys($this->types) as $chunkType) {
            $key = "boomcms::page.diff.chunk.$chunkType";

            $new = new Version([Version::ATTR_CHUNK_TYPE => $chunkType]);
            $old = new Version();
            $change = new ChunkChange($new, $old);

            $this->assertTrue(Lang::has($key));
            $this->assertEquals(Lang::get($key), $change->getSummary());
        }
    }
}
