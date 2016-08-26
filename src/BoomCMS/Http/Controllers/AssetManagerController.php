<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Support\Facades\Asset as AssetFacade;
use BoomCMS\Support\Facades\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class AssetManagerController extends Controller
{
    /**
     * @var string
     */
    protected $viewPrefix = 'boomcms::assets.';

    public function __construct(Request $request)
    {
        $this->request = $request;

        if (!$this->request->is('*/picker')) {
            $this->authorize('manageAssets', Router::getActiveSite());
        }
    }

    public function getDownload()
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

    /**
     * Display the asset manager.
     */
    public function index()
    {
        return view($this->viewPrefix.'index');
    }

    public function picker()
    {
        return view($this->viewPrefix.'picker');
    }
}
