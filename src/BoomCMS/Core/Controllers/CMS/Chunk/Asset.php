<?php

namespace BoomCMS\Core\Controllers\CMS\Chunk;

use BoomCMS\Core\Facades\Chunk as ChunkFacade;
use Illuminate\Support\Facades\View;

class Asset extends Chunk
{
    public function edit()
    {
        $chunk = ChunkFacade::get('asset', $this->request->query('slotname'), $this->page);

        return View::make('boom::editor.chunk.asset', [
            'chunk' => $chunk,
        ]);
    }
}
