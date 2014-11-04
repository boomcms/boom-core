<?php

class Model_Chunk_Timestamp extends ORM
{
    protected $_table_columns = [
        'id' => '',
        'timestamp' => '',
        'format' => '',
        'slotname'    => '',
        'page_vid' => '',
    ];

    protected $_table_name = 'chunk_timestamps';

    public function is_valid_format()
    {
        return in_array($this->format, Chunk_Timestamp::$formats);
    }

    public function filters()
    {
        return [
            'timstamp' => [
                ['strtotime'],
            ],
        ];
    }

    public function rules()
    {
        return [
            'timestamp' => [
                [[$this, 'is_valid_format']]
            ],
        ];
    }
}
