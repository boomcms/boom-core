<?php

/**
 * Records which assets are being referenced from within text chunks
 * When a text chunk is saved regular expressions are used to find links to CMS assets.
 * Recording these allows us to show in the asset manager where an asset is used.
 */
class Model_Chunk_Text_Asset extends \ORM
{
    protected $_primary_key = null;
    protected $_belongs_to = array('asset' => array());
    protected $_table_columns = array(
        'chunk_id'    =>    '',
        'asset_id'    =>    '',
        'position'    =>    '',
    );

    protected $_table_name = 'chunk_text_assets';
}
