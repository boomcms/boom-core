<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Helpers;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        return [
            'total'  => Helpers::countAssets($request->input()),
            'assets' => Helpers::getAssets($request->input()),
        ];
    }

    /**
     * @param Asset $asset
     *
     * @return View
     */
    public function show(Asset $asset)
    {
        return view('boomcms::assets.view', [
            'asset' => $asset,
        ]);
    }
}
