<?php

use BoomCMS\Core\Page\Page;
use Illuminate\Support\Facades\View;

class Chunk_ChunkControllerTest extends TestCase
{
    public function testEditViewsCanBeRenderedWithJustAChunkVariable()
    {
        $types = ['asset', 'linkset', 'location', 'slideshow', 'tag', 'timestamp'];

        View::share('button', function () {
            return '';
        });

        foreach ($types as $type) {
            $className = 'BoomCMS\Core\Chunk\\'.ucfirst($type);

            $chunk = new $className(new Page(), [], 'test', true);
            View::make('boom::editor.chunk.'.$type, [
                'chunk' => $chunk,
            ])->render();
        }
    }
}
