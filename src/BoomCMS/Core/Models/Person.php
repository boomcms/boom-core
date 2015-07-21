<?php

namespace BoomCMS\Core\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use SoftDeletes;

    public $table = 'people';
    public $guarded = ['id'];
    public $timestamps = false;

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }
}
