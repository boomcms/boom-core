<?php

namespace BoomCMS\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    public $table = 'assets';
    public $guarded = ['id'];
    public $timestamps = false;

    public function versions()
    {
        return $this->hasMany('BoomCMS\Core\Asset\Version');
    }

    public function scopeWithLatestVersion($query)
    {
        return $query->with(['versions' => function($query) {
            $query
                ->leftJoin('asset_versions as av2', 'av2.asset_id', '=', 'asset.id')
                ->where('av2.id', '>', 'asset_versions.id')
                ->whereNull('av2.id');
        }]);
    }
}
