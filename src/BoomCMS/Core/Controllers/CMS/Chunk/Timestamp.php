<?php

namespace BoomCMS\Core\Controllers\CMS\Chunk;

use BoomCMS\Core\Facades\Chunk as ChunkFacade;
use BoomCMS\Core\Chunk\Timestamp as ChunkTimestamp;
use Illuminate\Support\Facades\View;

class Timestamp extends Chunk
{
    public function edit()
    {
        $formats = [];
        foreach (ChunkTimestamp::$formats as $format) {
            $formats[$format] = date($format, time());
        }

        $chunk = ChunkFacade::get('timestamp', $this->request->query('slotname'), $this->page);

        return View::make('boom::editor.chunk.timestamp', [
            'timestamp' => $chunk->getTimestamp(),
            'format' => ChunkTimestamp::$defaultFormat,
            'formats' => $formats,
        ]);
    }
}
