<?php

namespace BoomCMS\Database\Models\Chunk;

class Html extends BaseChunk
{
    protected $table = 'chunk_html';

    public function setHtmlAttribute($value)
    {
        $this->attributes['html'] = trim($value);
    }
}
