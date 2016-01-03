<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Core\Page\Finder;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Approvals extends Controller
{
    protected $role = 'manageApprovals';

    public function getIndex(Request $request)
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
