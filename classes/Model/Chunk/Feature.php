<?php

class Model_Chunk_Feature extends \ORM
{
    protected $_table_columns = [
        'id'                =>    '',
        'target_page_id'    =>    '',
        'slotname'            =>    '',
        'page_vid' => '',
    ];

    protected $_belongs_to = ['target' => ['model' => 'Page', 'foreign_key' => 'target_page_id']];

    protected $_table_name = 'chunk_features';
}
