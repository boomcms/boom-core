<?php

namespace BoomCMS\Core\Model\Chunk;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $_table_columns = [
        'id' => '',
        'slotname'    => '',
        'tag' => '',
        'page_vid' => '',
    ];

    protected $table = 'chunk_tags';
}
