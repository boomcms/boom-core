<?php

class Model_Template extends ORM
{
    protected $_table_columns = [
        'id'            =>    '',
        'name'        =>    '',
        'description'    =>    '',
        'filename'        =>    '',
    ];

    protected $_table_name = 'templates';

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
