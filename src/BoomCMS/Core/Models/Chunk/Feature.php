<?php

namespace BoomCMS\Core\Models\Chunk;

class Feature extends BaseChunk
{
    protected $_belongs_to = ['target' => ['model' => 'Page', 'foreign_key' => 'target_page_id']];

    protected $table = 'chunk_features';
}
