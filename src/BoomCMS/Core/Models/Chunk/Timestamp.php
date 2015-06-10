<?php

namespace BoomCMS\Core\Models\Chunk;

use Illuminate\Database\Eloquent\Model;

class Timestamp extends Model
{
    protected $table = 'chunk_timestamps';

    public function is_valid_format()
    {
        return in_array($this->format, \Boom\Chunk\Timestamp::$formats);
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
