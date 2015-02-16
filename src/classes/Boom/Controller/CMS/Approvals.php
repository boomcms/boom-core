<?php

namespace Boom\Controller\CMS;

class Approvals extends Boom\Controller
{
    public function before()
    {
        parent::before();

        $this->authorization('manage_approvals');
    }

    public function action_index()
    {
        $this->template = new View('boom/approvals/index', [
            'pages' => $this->_get_pages_awaiting_approval(),
        ]);
    }

    /**
	 * Eek! This needs moving somewhere else...
	 *
	 * @return array
	 */
    protected function _get_pages_awaiting_approval()
    {
        $can_publish = DB::select('mptt.lft', 'mptt.rgt', 'mptt.scope')
            ->from(['page_mptt', 'mptt'])
            ->join('people_roles', 'inner')
            ->on('mptt.id', '=', 'people_roles.page_id')
            ->join('roles', 'inner')
            ->on('people_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'p_publish_page')
            ->group_by('mptt.id');

        return ORM::factory('Page')
            ->with_current_version(\Boom\Editor::instance())
            ->where('pending_approval', '=', true)
            ->join(['page_mptt', 'mptt1'])
            ->on('page.id', '=', 'mptt1.id')
            ->join([$can_publish, 'mptt2'], 'inner')
            ->on('mptt1.lft', '>=', 'mptt2.lft')
            ->on('mptt1.rgt', '<=', 'mptt2.rgt')
            ->on('mptt1.scope', '=', 'mptt2.scope')
            ->order_by('version.edited_time', 'desc')
            ->find_all()
            ->as_array();
    }
}
