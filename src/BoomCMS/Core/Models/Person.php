<?php

namespace BoomCMS\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    public $table = 'people';
    public $guarded = ['id'];
    public $timestamps = false;
}
