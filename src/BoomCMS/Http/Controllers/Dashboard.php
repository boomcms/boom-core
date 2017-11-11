<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\BoomCMS;
use BoomCMS\Database\Models\Site;
use BoomCMS\Support\Helpers;
use Illuminate\Support\Facades\Gate;

class Dashboard extends Controller
{
    public function index(BoomCMS $boomcms, Site $site)
    {
        return view('boomcms::dashboard.index', [
            'person'    => auth()->user(),
            'pages'     => Helpers::getPages([
                'limit' => 20,
                'order' => 'date desc',
            ]),
            'approvals' => Gate::denies('managePages', $site) ? [] : Helpers::getPages([
                'pendingapproval' => true,
            ]),
            'news'      => $boomcms->getNews(),
        ]);
    }
}
