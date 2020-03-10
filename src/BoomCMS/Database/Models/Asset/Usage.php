<?php

namespace BoomCMS\Database\Models\Asset;

use BoomCMS\Foundation\Database\Model;

class Usage extends Model
{
    protected $table = 'asset_usages';

    public function getNoOfUsage($id)
    {
        return $this->where('asset_id', $id)->count();
    }

    public function scopeRecentlyViewed($query, $assetId, $ip)
    {
        return $query
        ->where('ip_address', '=', $ip)
        ->where('asset_id', '=', $assetId)
        ->where('time', '>=', time() - 600);
    }
}
