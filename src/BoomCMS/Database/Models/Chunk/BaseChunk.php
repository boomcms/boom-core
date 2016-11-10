<?php

namespace BoomCMS\Database\Models\Chunk;

use BoomCMS\Contracts\Models\PageVersion;
use BoomCMS\Foundation\Database\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class BaseChunk extends Model
{
    public function scopeGetSince(Builder $query, PageVersion $version)
    {
        return $query
            ->select($this->table.'.*')
            ->where($this->table.'.page_vid', '>', $version->getId())
            ->where($this->table.'.page_id', '=', $version->getPageId())
            ->leftJoin($this->table.' as c1', function(JoinClause $join) use($version) {
                $join
                    ->on('c1.page_vid', '>', $this->table.'.page_vid')
                    ->on('c1.page_id', '=', $this->table.'.page_id')
                    ->on('c1.slotname', '=', $this->table.'.slotname');
            })
            ->whereNull('c1.page_vid');
    }

    public function scopeGetSingleChunk(Builder $query, PageVersion $version, $slotname)
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
