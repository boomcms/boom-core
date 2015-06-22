<?php

namespace BoomCMS\Core\Models\Asset;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    public $guarded = ['id'];
    protected $table = 'asset_downloads';
	public $timestamps = false;
	
	public function scopeRecentlyLogged($query, $assetId, $ip)
	{
		return $query
			->where('ip', '=', $ip)
			->where('asset_id', '=', $assetId)
			->where('time', '>=', time() - 600);
	}
}
