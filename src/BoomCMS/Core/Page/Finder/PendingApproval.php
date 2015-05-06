<?php

namespace BoomCMS\Core\Page\Finder;

use DB;

class PendingApproval extends \Boom\Finder\Filter
{
    public function execute(\ORM $query)
    {
        $can_publish = DB::select('mptt.lft', 'mptt.rgt', 'mptt.scope')
            ->from(['page_mptt', 'mptt'])
            ->join('people_roles', 'inner')
            ->on('mptt.id', '=', 'people_roles.page_id')
            ->join('roles', 'inner')
            ->on('people_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'p_publish_page')
            ->group_by('mptt.id');

        return $query
            ->where('pending_approval', '=', true)
            ->join(['page_mptt', 'mptt1'])
            ->on('page.id', '=', 'mptt1.id')
            ->join([$can_publish, 'mptt2'], 'inner')
            ->on('mptt1.lft', '>=', 'mptt2.lft')
            ->on('mptt1.rgt', '<=', 'mptt2.rgt')
            ->on('mptt1.scope', '=', 'mptt2.scope')
            ->order_by('version.edited_time', 'desc');
    }
}
