<?php

namespace BoomCMS\Http\Controllers\Sites;

use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Site;

class SiteManager extends Controller
{
    protected $viewPrefix = 'boomcms::sites.';
    protected $role = 'manageSites';

    public function index()
    {
        return view("{$this->viewPrefix}index", [
            'sites' => Site::findAll(),
        ]);
    }
}
