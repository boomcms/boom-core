<?php

class Model_Chunk_Tag extends \ORM
{
    protected $_table_columns = [
        'id' => '',
        'slotname'    => '',
        'tag_id' => '',
        'page_vid' => '',
    ];

    protected $_table_name = 'chunk_tags';
}
