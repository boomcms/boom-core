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
        $assets = AssetFacade::find($this->request->input('asset'));

        if (count($assets) === 1) {
            return Response::download(
                $assets[0]->getFilename(),
                $assets[0]->getOriginalFilename()
            );
        }

        $downloadFilename = rtrim($this->request->input('filename'), '.zip').'.zip';
        $filename = tempnam(sys_get_temp_dir(), 'boomcms_asset_download');
        $zip = new ZipArchive();
        $zip->open($filename, ZipArchive::CREATE);

        foreach ($assets as $asset) {
            $zip->addFile($asset->getFilename(), $asset->getOriginalFilename());
        }

        $zip->close();

        $response = Response::make()
            ->header('Content-type', 'application/zip')
            ->header('Content-Disposition', "attachment; filename=$downloadFilename")
            ->setContent(file_get_contents($filename));

        unlink($filename);

        return $response;
    }

}
