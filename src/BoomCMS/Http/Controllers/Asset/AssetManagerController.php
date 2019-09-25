<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Http\Controllers\Controller;
use Illuminate\View\View;
use BoomCMS\Database\Models\Asset;

class AssetManagerController extends Controller
{
    /**
     * @var string
     */
    protected $viewPrefix = 'boomcms::assets.';

    protected $role = 'manageAssets';

    /**
     * Display the asset manager.
     *
     * @return View
     */
    public function index()
    {
        return view($this->viewPrefix.'index');
    }

}
