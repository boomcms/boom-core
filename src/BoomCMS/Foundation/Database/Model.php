<?php

namespace BoomCMS\Foundation\Database;

use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    const ATTR_ID = 'id';

    public $guarded = [
        self::ATTR_ID,
    ];

    protected $casts = [
        self::ATTR_ID => 'integer',
    ];

    public $timestamps = false;

    public function is(Model $other)
    {
        return (get_class($this) === get_class($other))
            && ($this->getId() > 0)
            && ($this->getId() === $other->getId());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->{static::ATTR_ID};
    }
}
