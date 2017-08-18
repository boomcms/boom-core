<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Database\Models\Page;
use BoomCMS\Support\Facades\Album;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\View;

class ChunkControllerTest extends AbstractTestCase
{
    public function testEditViewsCanBeRenderedWithJustAChunkVariable()
    {
        $types = ['asset', 'library', 'linkset', 'location', 'slideshow', 'timestamp'];

        Album::shouldReceive('all')
            ->once()
            ->andReturn(collect([]));

        View::share('button', function () {
            return '';
        });

        foreach ($types as $type) {
            $className = 'BoomCMS\Chunk\\'.ucfirst($type);

            $chunk = new $className(new Page(), [], 'test', true);
            View::make('boomcms::editor.chunk.'.$type, [
                'chunk' => $chunk,
            ])->render();
        }
    }
}
