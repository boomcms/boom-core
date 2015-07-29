<?php

namespace BoomCMS\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use SoftDeletes;

    protected $table = 'groups';
    public $guarded = ['id'];
    public $timestamps = false;
}
