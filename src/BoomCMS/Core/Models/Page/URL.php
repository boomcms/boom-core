<?php

namespace BoomCMS\Core\Models\Page;

use Illuminate\Database\Eloquent\Model;

class URL extends Model
{
    protected $table = 'page_urls';
    public $guarded = ['id'];
    public $timestamps = false;
}
