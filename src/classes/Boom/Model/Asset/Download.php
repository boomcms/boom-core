<?php

class Model_Asset_Download extends \ORM
{
    protected $_belongs_to = [
        'asset'        =>    [],
    ];

    protected $_created_column = [
        'column'    =>    'time',
        'format'    =>    true,
    ];

    protected $_table_columns = [
        'id' => '',
        'asset_id' => '',
        'time' => '',
        'ip' => '',
    ];

    protected $_table_name = 'asset_downloads';
}
