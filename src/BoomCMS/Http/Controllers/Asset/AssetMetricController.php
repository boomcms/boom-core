<?php

namespace BoomCMS\Http\Controllers\Asset;

use DB;
use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\Site;
use BoomCMS\Foundation\Http\ValidatesAssetUpload;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Album as AlbumFacade;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use BoomCMS\Support\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class AssetMetricController extends Controller
{

    /**
     * @var string
     */
    protected $viewPrefix = 'boomcms::assets.metrics.';

    public function index(Request $request)
    {
        $viewName = $this->viewPrefix.'index';

        print_r($request->all());

        $sort = $request->get('sort');

        if($sort == 'filename') {
            $sort = 'asset_versions.filename';
            $order = 'asc';
        } elseif($sort == 'extension') {
            $sort = 'filename';
            $order = 'asc';
        } elseif($sort == 'uploaded') {
            $sort = 'asset_versions.created_at';
            $order = 'desc';
        } elseif($sort == 'downloads') {
            $sort = 'downloads';
            $order = 'desc';
        } else {
            $sort = 'asset_versions.filename';
            $order = 'asc';
        }

        $assets = Asset::join('asset_versions', 'asset_versions.asset_id', 'assets.id')
        ->join('asset_downloads', 'asset_downloads.asset_id', 'assets.id')
        ->where('type', 'doc')
        ->groupBy('assets.id')
        ->orderBy($sort, $order)
        ->get([
            'assets.id', 
            'asset_versions.filename', 
            'asset_versions.created_at', 
            'asset_versions.extension', 
            DB::raw('count(asset_downloads.asset_id) as downloads')
        ]);

        return view()->make($viewName, [
            'assets'  => $assets,
        ]);
    }
}
