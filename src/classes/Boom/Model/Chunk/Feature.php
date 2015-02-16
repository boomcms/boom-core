<?php

namespace Boom\Model\Chunk;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $_table_columns = [
        'id'                =>    '',
        'target_page_id'    =>    '',
        'slotname'            =>    '',
        'page_vid' => '',
    ];

    protected $_belongs_to = ['target' => ['model' => 'Page', 'foreign_key' => 'target_page_id']];

    protected $table = 'chunk_features';
}
