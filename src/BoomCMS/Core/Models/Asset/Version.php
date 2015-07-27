<?php

namespace BoomCMS\Core\Models\Asset;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    public $table = 'asset_versions';
    public $guarded = ['id'];
    public $timestamps = false;
}
