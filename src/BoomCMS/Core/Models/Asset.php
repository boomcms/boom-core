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
        return $this->hasMany('BoomCMS\Core\Models\Asset\Version');
    }

    public function scopeWithLatestVersion($query)
    {
        return $query
            ->select('version.*')
            ->addSelect('version.id as version:id')
            ->addSelect('assets.*')
            ->join('asset_versions as version', 'assets.id', '=', 'version.asset_id')
            ->leftJoin('asset_versions as av2', function($query) {
                $query
                    ->on('av2.asset_id', '=', 'version.asset_id')
                    ->on('av2.id', '>', 'version.id');
            })
            ->whereNull('av2.id');
    }

    public function scopeWithVersion($query, $versionId)
    {
        return $query
            ->select('version.*')
            ->addSelect('version.id as version:id')
            ->addSelect('assets.*')
            ->join('asset_versions as version', 'assets.id', '=', 'version.asset_id')
            ->where('version.id', '=', $versionId);
    }
}
