<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Core\Page\Finder;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class Approvals extends Controller
{
    public function getIndex()
    {
        $this->authorize('manageApprovals');

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
