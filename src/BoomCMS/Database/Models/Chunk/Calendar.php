<?php

namespace BoomCMS\Database\Models\Chunk;

class Calendar extends BaseChunk
{
    protected $table = 'chunk_calendars';

    const ATTR_CONTENT = 'content';

    protected $casts = [
        self::ATTR_CONTENT => 'json',
    ];
}
