<?php

namespace BoomCMS\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $_table_columns = [
        'id'            =>    '',
        'name'        =>    '',
        'description'    =>    '',
    ];

    protected $table = 'roles';
}
