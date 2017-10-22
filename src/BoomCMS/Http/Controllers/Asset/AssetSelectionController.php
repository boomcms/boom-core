<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Foundation\Http\ValidatesAssetUpload;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class AssetSelectionController extends Controller
{
    use ValidatesAssetUpload;

    protected $role = 'manageAssets';

    public function destroy(Request $request, Asset $asset = null)
    {
        $assetIds = ($asset->getId()) ? [$asset->getId()] : $request->input('assets');

        AssetFacade::delete($assetIds);
    }

    public function download()
    {
        $assets = AssetFacade::find($this->request->input('assets'));

        if (count($assets) === 0) {
            abort(404);
        }

        if (count($assets) === 1) {
            return Response::file(AssetFacade::path($assets[0]), [
                'Content-Type'        => $assets[0]->getMimetype(),
                'Content-Disposition' => 'download; filename="'.$assets[0]->getOriginalFilename().'"',
            ]);
        }

        $downloadFilename = rtrim($this->request->input('filename'), '.zip').'.zip';
        $filename = tempnam(sys_get_temp_dir(), 'boomcms_asset_download');
        $zip = new ZipArchive();
        $zip->open($filename, ZipArchive::CREATE);

        foreach ($assets as $asset) {
            $zip->addFile(AssetFacade::path($asset), $asset->getOriginalFilename());
        }

        $zip->close();

        return Response::download($filename, $downloadFilename)->deleteFileAfterSend(true);
    }
}
