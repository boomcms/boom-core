<?php

namespace BoomCMS\Core\Controllers\CMS\Chunk;

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

        return View::make('boom::editor.chunk.timestamp', [
            'timestamp' => time(),
            'format' => ChunkTimestamp::$defaultFormat,
            'formats' => $formats,
        ]);
    }
}
