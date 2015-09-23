<?php

namespace BoomCMS\Database\Models;

use Illuminate\Database\Eloquent\Model;

class SearchText extends Model
{
    protected $table = 'search_texts';
    protected $guarded = ['id'];
    public $timestamps = false;
}
