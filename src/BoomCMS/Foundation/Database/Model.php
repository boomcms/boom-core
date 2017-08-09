<?php

namespace BoomCMS\Foundation\Database;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Traits\Macroable;

abstract class Model extends BaseModel
{
    use Macroable;

    const ATTR_ID = 'id';

    public $guarded = [
        self::ATTR_ID,
    ];

    protected $casts = [
        self::ATTR_ID => 'integer',
    ];

    public $timestamps = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->{static::ATTR_ID};
    }
}
