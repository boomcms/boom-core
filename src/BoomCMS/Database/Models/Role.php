<?php

namespace BoomCMS\Database\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    public $guarded = ['id'];
    public $timestamps = false;

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
