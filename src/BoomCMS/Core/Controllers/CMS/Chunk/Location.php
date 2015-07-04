<?php

namespace BoomCMS\Core\Controllers\CMS\Chunk;

use BoomCMS\Core\Facades\Chunk as ChunkFacade;
use Illuminate\Support\Facades\View;

class Location extends Chunk
{
    public function edit()
    {
        $chunk = ChunkFacade::get('location', $this->request->query('slotname'), $this->page);

        return View::make('boom::editor.chunk.location', [
            'chunk' => $chunk,
        ]);
    }
}
