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
}
