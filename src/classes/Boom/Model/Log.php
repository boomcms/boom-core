<?php

namespace Boom\Model;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
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

    protected $table = 'logs';
}
