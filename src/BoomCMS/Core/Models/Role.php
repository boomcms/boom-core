<?php

namespace BoomCMS\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    public $guarded = ['id'];
    public $timestamps = false;

    public function scopeGetGeneralRoles($query)
    {
        return $query
            ->where('name', 'not like', 'p_%')
            ->orderBy('description', 'asc')
            ->get();
    }

    public function scopeGetPageRoles($query)
    {
        return $query
            ->where('name', 'like', 'p_%')
            ->orderBy('description', 'asc')
            ->get();
    }
}
