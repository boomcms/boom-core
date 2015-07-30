<?php

namespace BoomCMS\Database\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'templates';
    protected $guarded = ['id'];
    public $timestamps = false;
}
