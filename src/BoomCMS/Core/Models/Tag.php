<?php

namespace BoomCMS\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';
    public $guarded = ['id'];
    public $timestamps = false;
	
	public function setNameAttribute($value)
	{
		$this->attributes['name'] = trim(strip_tags($value));
	}
}
