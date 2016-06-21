<?php

namespace BoomCMS\Database\Models\Chunk;

use BoomCMS\Contracts\Models\PageVersion;
use BoomCMS\Foundation\Database\Model;

class BaseChunk extends Model
{
    public function scopeGetSingleChunk($query, PageVersion $version, $slotname)
    {
        return $query
            ->withRelations()
            ->where('slotname', '=', $slotname)
            ->where('page_vid', '<=', $version->getId())
            ->where('page_id', '=', $version->getPageId())
            ->orderBy('page_vid', 'desc');
    }

    public function scopeWithRelations($query)
    {
        return $query;
    }
}
