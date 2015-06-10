<?php

namespace BoomCMS\Core\Models\Chunk;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $_belongs_to = ['target' => ['model' => 'Page', 'foreign_key' => 'target_page_id']];

    protected $table = 'chunk_features';
}
