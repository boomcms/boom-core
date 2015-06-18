<?php

namespace BoomCMS\Core\Models\Page;

use BoomCMS\Core\URL\Helpers as URL;
use Illuminate\Database\Eloquent\Model;

class URL extends Model
{
    protected $table = 'page_urls';
    public $guarded = ['id'];
    public $timestamps = false;
	
	public function setLocationAttribute($value)
	{
		$this->location = URL::sanitise($value);
	}
}
