<?php

namespace BoomCMS\Core\Controllers\CMS\Chunk;

use BoomCMS\Core\Facades\Chunk as ChunkFacade;
use Illuminate\Support\Facades\View;

class Linkset extends Chunk
{
    public function edit()
    {
        $chunk = ChunkFacade::get('linkset', $this->request->query('slotname'), $this->page->getCurrentVersion());

        return View::make('boom::editor.chunk.linkset', [
            'links' => $chunk->getLinks(),
            'title' => $chunk->getTitle()
        ]);
    }
}
