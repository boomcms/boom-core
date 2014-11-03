<?php

class Model_Chunk_Tag extends \ORM
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

    protected $_table_name = 'chunk_tags';
}
