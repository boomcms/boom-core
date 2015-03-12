<?php

class Model_Chunk_Tag extends \ORM
{
    protected $_table_columns = [
        'id' => '',
        'slotname'    => '',
        'tag' => '',
        'page_vid' => '',
    ];

    protected $_table_name = 'chunk_tags';
}
