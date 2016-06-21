<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Helpers;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function getIndex(Request $request)
    {
        return Helpers::getAssets($request->input());
    }
}
