<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Support\Facades\Router;

class AssetManagerController extends Controller
{
    /**
     * @var string
     */
    protected $viewPrefix = 'boomcms::assets.';

    /**
     * Display the asset manager.
     */
    public function index()
    {
        $this->authorize('manageAssets', Router::getActiveSite());

        return view($this->viewPrefix.'index');
    }

    public function picker()
    {
        return view($this->viewPrefix.'picker');
    }
}
