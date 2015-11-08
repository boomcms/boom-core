<?php

namespace BoomCMS\Database\Models\Chunk;

class Library extends BaseChunk
{
    protected $table = 'chunk_libraries';

    public function getParamsAttribute($value)
    {
        return json_decode($value);
    }

    public function setParamsAttribute($value)
    {
        $this->attributes['params'] = json_encode($value);
    }
}
