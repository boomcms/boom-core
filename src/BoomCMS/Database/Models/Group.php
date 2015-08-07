<?php

namespace BoomCMS\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    protected $table = 'groups';
    public $guarded = ['id'];
    public $timestamps = false;
}
