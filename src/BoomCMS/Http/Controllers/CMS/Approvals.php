<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Core\Page;
use BoomCMS\Core\Controller\Controller;

class Approvals extends Controller
{
    public function before()
    {
        parent::before();

        $this->authorization('manage_approvals');
    }

    public function index()
    {
        return View::make('boom/approvals/index', [
            'pages' => $this->_get_pages_awaiting_approval(),
        ]);
    }

    protected function _get_pages_awaiting_approval()
    {
        $finder = new Page\Finder();
        $finder->addFilter(new Page\Finder\Filter\PendingApproval());

        return $finder->findAll();
    }
}
