<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Core\Page\Finder;
use BoomCMS\Http\Controllers\Controller;

class Approvals extends Controller
{
    protected $role = 'manageApprovals';

    public function getIndex()
    {
        return view('boomcms::approvals.index', [
            'pages' => $this->getPagesAwaitingApproval(),
        ]);
    }

    protected function getPagesAwaitingApproval()
    {
        $finder = new Finder\Finder();
        $finder->addFilter(new Finder\PendingApproval());

        return $finder->findAll();
    }
}
