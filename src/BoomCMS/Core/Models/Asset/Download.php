<?php

namespace BoomCMS\Core\Model\Asset;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
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

    protected $table = 'asset_downloads';
}
