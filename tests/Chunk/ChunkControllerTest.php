<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Core\Page\Page;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\View;

class ChunkControllerTest extends AbstractTestCase
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