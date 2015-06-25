<?php

use BoomCMS\Core\Chunk\Timestamp as ChunkTimestamp;

class Controller_Cms_Chunk_Timestamp extends Controller_Cms_Chunk
{
    protected $_type = 'timestamp';

    public function edit()
    {
        $formats = [];
        foreach (ChunkTimestamp::$formats as $format) {
            $formats[$format] = date($format, $_SERVER['REQUEST_TIME']);
        }

        return View::make('boom/editor/slot/timestamp', [
            'timestamp' => 0,
            'format' => ChunkTimestamp::$defaultFormat,
            'formats' => $formats,
        ]);
    }
}
