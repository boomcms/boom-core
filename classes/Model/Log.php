<?php

class Model_Log extends \ORM
{
    protected $_created_column = [
        'column'    =>    'time',
        'format'    =>    true,
    ];

    protected $_table_columns = [
        'id'            =>    '',
        'ip'            =>    '',
        'activity'        =>    '',
        'note'        =>    '',
        'person_id'    =>    '',
        'time'        =>    '',
    ];

    protected $_table_name = 'logs';
}
