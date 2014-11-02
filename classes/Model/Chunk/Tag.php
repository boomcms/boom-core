<?php

class Model_Chunk_Tag extends \ORM
{
    protected $_belongs_to = array(
        'target' => array('model' => 'Tag', 'foreign_key' => 'tag_id'),
    );

    protected $_table_columns = array(
        'id' => '',
        'slotname'    => '',
        'tag_id' => '',
        'page_vid' => '',
    );

    protected $_table_name = 'chunk_tags';
}
