<?php

namespace BoomCMS\Database\Models\Page;

use BoomCMS\Support\Helpers\URL as URLHelper;
use Illuminate\Database\Eloquent\Model;

class URL extends Model
{
    protected $table = 'page_urls';
    public $guarded = ['id'];
    public $timestamps = false;

    public function setLocationAttribute($value)
    {
        $this->attributes['location'] = URLHelper::sanitise($value);
    }
}
