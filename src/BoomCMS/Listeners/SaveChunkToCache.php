<?php

namespace BoomCMS\Listeners;

use BoomCMS\Events\ChunkWasCreated;
use BoomCMS\Support\Facades\Chunk;

class SaveChunkToCache
{
    public function handle(ChunkWasCreated $event)
    {
        $chunk = $event->getChunk();
        $type = $chunk->getType();
        $slotname = $chunk->getSlotname();
        $version = $event->getVersion();

        Chunk::saveToCache($type, $slotname, $version, $chunk);
    }
}
