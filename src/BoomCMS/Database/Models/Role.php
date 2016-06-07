<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Foundation\Database\Model;

class Role extends Model
{
    protected $table = 'roles';

    public function scopeGetGeneralRoles($query)
    {
        return $query
            ->where('tree', '=', false)
            ->orderBy('description', 'asc')
            ->get();
    }

    public function scopeGetPageRoles($query)
    {
        return $query
            ->where('tree', '=', true)
            ->orderBy('description', 'asc')
            ->get();
    }
}
