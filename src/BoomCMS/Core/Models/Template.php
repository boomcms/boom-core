<?php

namespace BoomCMS\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $_table_columns = [
        'id'            =>    '',
        'name'        =>    '',
        'description'    =>    '',
        'filename'        =>    '',
    ];

    protected $table = 'templates';

    public function rules()
    {
        return [
            'name' => [
                ['not_empty'],
            ],
            'filename' => [
                ['not_empty'],
            ],
        ];
    }
}
