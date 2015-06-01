<?php

namespace BoomCMS\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    public $table = 'assets';
    public $guarded = ['id'];
    public $timestamps = false;
}
