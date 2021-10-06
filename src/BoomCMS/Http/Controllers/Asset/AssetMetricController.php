<?php

namespace BoomCMS\Http\Controllers\Asset;

use DB;
use Validator;
use Session;
use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\Asset\Download as AssetDownload;
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
        $viewName = $this->viewPrefix . 'index';

        $sort = $request->get('sort');

        if ($sort == 'filename') {
            $sort = 'asset_versions.filename';
            $order = 'asc';
        } elseif ($sort == 'extension') {
            $sort = 'filename';
            $order = 'asc';
        } elseif ($sort == 'uploaded') {
            $sort = 'asset_versions.created_at';
            $order = 'desc';
        } elseif ($sort == 'downloads') {
            $sort = 'downloads';
            $order = 'desc';
        } else {
            $sort = 'asset_versions.filename';
            $order = 'asc';
        }

        if ($request->get('clear') == 1) {
            Session::forget('from_date');
            Session::forget('to_date');
        }

        $from_date = session('from_date');
        $to_date = session('to_date');

        if (trim($from_date) !== '' && trim($to_date) !== '') {
            $downloads = Asset::join('asset_versions', 'asset_versions.asset_id', 'assets.id')
                ->join('asset_downloads', 'asset_downloads.asset_id', 'assets.id')
                ->where('assets.type', 'doc')
                ->where('asset_versions.created_at', '>=', strtotime($from_date))
                ->where('asset_versions.created_at', '<=', strtotime($to_date))
                ->groupBy('assets.id')
                ->orderBy($sort, $order)
                ->get([
                    'assets.id',
                    'asset_versions.filename',
                    'asset_versions.created_at',
                    'asset_versions.extension',
                    DB::raw('count(asset_downloads.asset_id) as downloads')
                ]);
        } else {
            $assets = Asset::join('asset_versions', 'asset_versions.asset_id', 'assets.id')
                ->join('asset_downloads', 'asset_downloads.asset_id', 'assets.id')
                ->where('assets.type', 'doc')
                ->groupBy('assets.id')
                ->orderBy($sort, $order)
                ->get([
                    'assets.id',
                    'asset_versions.filename',
                    'asset_versions.created_at',
                    'asset_versions.extension',
                    DB::raw('count(asset_downloads.asset_id) as downloads')
                ]);
        }

        return view()->make($viewName, [
            'assets'  => $assets,
        ]);
    }

    public function filterDownloads(Request $request)
    {
        $viewName = $this->viewPrefix . 'index';

        $sort = $request->get('sort');

        if ($sort == 'filename') {
            $sort = 'asset_versions.filename';
            $order = 'asc';
        } elseif ($sort == 'extension') {
            $sort = 'filename';
            $order = 'asc';
        } elseif ($sort == 'uploaded') {
            $sort = 'asset_versions.created_at';
            $order = 'desc';
        } elseif ($sort == 'downloads') {
            $sort = 'downloads';
            $order = 'desc';
        } else {
            $sort = 'asset_versions.filename';
            $order = 'asc';
        }

        $validator = Validator::make($request->all(), [
            'from' => 'required|date_format:d F Y H:i',
            'to' => 'required|date_format:d F Y H:i',
        ]);

        if ($validator->fails()) {
            return redirect('/boomcms/asset-manager/metrics')
                ->withErrors($validator)
                ->withInput();
        }

        $from_date = trim($request->get('from'));
        $to_date = trim($request->get('to'));

        if ($from_date !== '' && $to_date !== '') {

            Session::put('from_date', $from_date);
            Session::put('to_date', $to_date);


            $assets = Asset::join('asset_versions', 'asset_versions.asset_id', 'assets.id')
                ->join('asset_downloads', 'asset_downloads.asset_id', 'assets.id')
                ->where('assets.type', 'doc')
                ->where('asset_versions.created_at', '>=', strtotime($from_date))
                ->where('asset_versions.created_at', '<=', strtotime($to_date))
                ->groupBy('assets.id')
                ->orderBy($sort, $order)
                ->get([
                    'assets.id',
                    'asset_versions.filename',
                    'asset_versions.created_at',
                    'asset_versions.extension',
                    DB::raw('count(asset_downloads.asset_id) as downloads')
                ]);
        } else {

            $assets = Asset::join('asset_versions', 'asset_versions.asset_id', 'assets.id')
                ->join('asset_downloads', 'asset_downloads.asset_id', 'assets.id')
                ->where('assets.type', 'doc')
                ->groupBy('assets.id')
                ->orderBy($sort, $order)
                ->get([
                    'assets.id',
                    'asset_versions.filename',
                    'asset_versions.created_at',
                    'asset_versions.extension',
                    DB::raw('count(asset_downloads.asset_id) as downloads')
                ]);
        }

        return view()->make($viewName, [
            'assets'  => $assets,
        ]);
    }

    public function csvExportAssets()
    {
        $assets = Asset::join('asset_versions', 'asset_versions.asset_id', 'assets.id')
                ->join('asset_downloads', 'asset_downloads.asset_id', 'assets.id')
                ->where('assets.type', 'doc')
                ->groupBy('assets.id')
                ->orderBy('asset_versions.filename', 'asc')
                ->get([
                    'assets.id',
                    'asset_versions.filename',
                    'asset_versions.created_at',
                    'asset_versions.extension',
                    DB::raw('count(asset_downloads.asset_id) as downloads')
                ]);

        if ($assets && $assets->count() > 0) {

            $filename = 'asset-download-'.date('Y-m-d-H-i-s').'.csv';
            $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Content-type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename='.$filename,
                'Expires'             => '0',
                'Pragma'              => 'public'
        ];
    
    
            $list = $assets->toArray();

            $callback = function () use ($list) {

                $csv = fopen('php://output', 'w');

                $header = array(
                    'FILENAME',
                    'EXTENSION',
                    'UPLOADED ON',
                    'DOWNLOAD'
                        );

                fputcsv($csv, $header);

                foreach ($list as $row) {

                    $asset = array(
                            $row['filename'],
                            $row['extension'],
                            date('d F Y', $row['created_at']),
                            $row['downloads'],
                        );

                    fputcsv($csv, $asset);
                }
                fclose($csv);
            };

           return Response::stream($callback, 200, $headers);
        }

        return redirect('/')->with('warning', 'No asset found to download!');
    }

    public function show(Request $request, $asset_id)
    {
        $viewName = $this->viewPrefix . 'show';

        $sort = $request->get('sort');

        if ($sort == 'date') {
            $sort = 'created_at';
            $order = 'desc';
        } elseif ($sort == 'downloads') {
            $sort = 'downloads';
            $order = 'desc';
        } else {
            $sort = 'created_at';
            $order = 'desc';
        }

        $asset = Asset::join('asset_versions', 'asset_versions.asset_id', 'assets.id')
            ->join('asset_downloads', 'asset_downloads.asset_id', 'assets.id')
            ->where('assets.type', 'doc')
            ->where('assets.id', $asset_id)
            ->first([
                'assets.id',
                'asset_versions.filename',
                'asset_versions.extension'
            ]);

        if ($request->get('clear') == 1) {
            Session::forget('from_date');
            Session::forget('to_date');
        }

        $from_date = session('from_date');
        $to_date = session('to_date');

        if (trim($from_date) !== '' && trim($to_date) !== '') {

            $downloads = AssetDownload::where('asset_id', $asset_id)
                ->where('created_at', '>=', date('Y-m-d', strtotime($from_date)))
                ->where('created_at', '<=', date('Y-m-d', strtotime($to_date)))
                ->groupBy('created_at')
                ->orderBy($sort, $order)
                ->get([
                    'created_at',
                    DB::raw('count(id) as downloads')
                ]);
        } else {
            $downloads = AssetDownload::where('asset_id', $asset_id)
                ->groupBy('created_at')
                ->orderBy($sort, $order)
                ->get([
                    'created_at',
                    DB::raw('count(id) as downloads')
                ]);
        }

        return view()->make($viewName, [
            'asset' => $asset,
            'downloads'  => $downloads,
        ]);
    }

    public function filterAssetDownloads(Request $request, $asset_id)
    {
        $viewName = $this->viewPrefix . 'show';

        $sort = $request->get('sort');

        if ($sort == 'date') {
            $sort = 'created_at';
            $order = 'desc';
        } elseif ($sort == 'downloads') {
            $sort = 'downloads';
            $order = 'desc';
        } else {
            $sort = 'created_at';
            $order = 'desc';
        }

        $asset = Asset::join('asset_versions', 'asset_versions.asset_id', 'assets.id')
            ->join('asset_downloads', 'asset_downloads.asset_id', 'assets.id')
            ->where('assets.type', 'doc')
            ->where('assets.id', $asset_id)
            ->first([
                'assets.id',
                'asset_versions.filename',
                'asset_versions.extension'
            ]);

        $validator = Validator::make($request->all(), [
            'from' => 'required|date_format:d F Y H:i',
            'to' => 'required|date_format:d F Y H:i',
        ]);

        if ($validator->fails()) {
            return redirect('/boomcms/asset-manager/metrics/' . $asset_id . '/details')
                ->withErrors($validator)
                ->withInput();
        }

        $from_date = trim($request->get('from'));
        $to_date = trim($request->get('to'));

        if ($from_date !== '' && $to_date !== '') {

            Session::put('from_date', $from_date);
            Session::put('to_date', $to_date);

            $downloads = AssetDownload::where('asset_id', $asset_id)
                ->where('created_at', '>=', date('Y-m-d', strtotime($from_date)))
                ->where('created_at', '<=', date('Y-m-d', strtotime($to_date)))
                ->groupBy('created_at')
                ->orderBy($sort, $order)
                ->get([
                    'created_at',
                    DB::raw('count(id) as downloads')
                ]);
        } else {

            $downloads = AssetDownload::where('asset_id', $asset_id)
                ->groupBy('created_at')
                ->orderBy($sort, $order)
                ->get([
                    'created_at',
                    DB::raw('count(id) as downloads')
                ]);
        }

        return view()->make($viewName, [
            'asset' => $asset,
            'downloads'  => $downloads,
        ]);
    }

    public function csvExportAsset($asset_id)
    {
        $asset = Asset::join('asset_versions', 'asset_versions.asset_id', 'assets.id')
            ->join('asset_downloads', 'asset_downloads.asset_id', 'assets.id')
            ->where('assets.type', 'doc')
            ->where('assets.id', $asset_id)
            ->first([
                'assets.id',
                'asset_versions.filename',
                'asset_versions.extension'
            ]);

        $downloads = AssetDownload::where('asset_id', $asset_id)
                ->groupBy('created_at')
                ->orderBy('created_at', 'desc')
                ->get([
                    'created_at',
                    DB::raw('count(id) as downloads')
                ]);
                

        if ($downloads && $downloads->count() > 0) {

            $filename = strtolower(str_replace(' ', '-', $asset->filename)).'-downloads-'.date('Y-m-d-H-i-s').'.csv';
            $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Content-type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename='.$filename,
                'Expires'             => '0',
                'Pragma'              => 'public'
        ];
    
    
            $list = $downloads->toArray();

            $callback = function () use ($list) {

                $csv = fopen('php://output', 'w');

                $header = array(
                    'DOWNLOAD DATE',
                    'NUMBER OF DOWNLOADS',
                        );

                fputcsv($csv, $header);

                foreach ($list as $row) {

                    $asset = array(
                            date('d F Y', strtotime($row['created_at'])),
                            $row['downloads'],
                        );

                    fputcsv($csv, $asset);
                }
                fclose($csv);
            };

           return Response::stream($callback, 200, $headers);
        }

        return redirect('/')->with('warning', 'No asset found to download!');
    }
}
