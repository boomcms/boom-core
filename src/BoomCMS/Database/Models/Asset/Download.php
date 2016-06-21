<?php

namespace BoomCMS\Database\Models\Asset;

use BoomCMS\Foundation\Database\Model;

class Download extends Model
{
    protected $table = 'asset_downloads';

    public function scopeRecentlyLogged($query, $assetId, $ip)
    {
        return $query
            ->where('ip', '=', $ip)
            ->where('asset_id', '=', $assetId)
            ->where('time', '>=', time() - 600);
    }
}
