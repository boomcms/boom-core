<?php

namespace Boom\Model\Chunk;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $_belongs_to = [
        'target' => ['model' => 'Tag', 'foreign_key' => 'tag_id'],
    ];

    protected $_table_columns = [
        'id' => '',
        'slotname'    => '',
        'tag_id' => '',
        'page_vid' => '',
    ];

    protected $table = 'chunk_tags';
}
