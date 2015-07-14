<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Finder\Filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PendingApproval extends Filter
{
    public function build(Builder $query)
    {
        $can_publish = DB::select('mptt.lft', 'mptt.rgt', 'mptt.scope')
            ->from(['page_mptt', 'mptt'])
            ->join('people_roles', 'mptt.id', '=', 'people_roles.page_id')
            ->join('roles', 'people_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'p_publish_page')
            ->groupBy('mptt.id');

        return $query
            ->where('pending_approval', '=', true)
            ->join(['page_mptt', 'mptt1'])
            ->on('page.id', '=', 'mptt1.id')
            ->join([$can_publish, 'mptt2'], 'inner')
            ->on('mptt1.lft', '>=', 'mptt2.lft')
            ->on('mptt1.rgt', '<=', 'mptt2.rgt')
            ->on('mptt1.scope', '=', 'mptt2.scope')
            ->orderBy('version.edited_time', 'desc');
    }
}
