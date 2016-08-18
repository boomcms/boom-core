<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Helpers;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        return [
            'total'  => Helpers::countAssets($request->input()),
            'assets' => Helpers::getAssets($request->input()),
        ];
    }
}
