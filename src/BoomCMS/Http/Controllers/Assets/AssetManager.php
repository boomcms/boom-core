<?php

namespace BoomCMS\Http\Controllers\Assets;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\Site;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use BoomCMS\Support\Facades\Router;
use BoomCMS\Support\Facades\Site as SiteFacade;
use BoomCMS\Support\Helpers;
use BoomCMS\Support\Helpers\Asset as AssetHelper;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class AssetManager extends Controller
{
    /**
     * @var string
     */
    protected $viewPrefix = 'boomcms::assets.';

    public function __construct(Request $request)
    {
        $this->request = $request;

        if (!$this->request->is('*/picker') && !$this->request->is('*/get')) {
            $this->authorize('manageAssets', Router::getActiveSite());
        }
    }

    public function addSites(Request $request, Asset $asset)
    {
        $siteIds = $request->input('sites');

        if ($siteIds) {
            $sites = SiteFacade::find($siteIds);
            $asset->addSites($sites);
        }
    }

    public function delete()
    {
        AssetFacade::delete($this->request->input('assets'));
    }

    public function download()
    {
        $assets = AssetFacade::find($this->request->input('asset'));

        if (count($assets) === 1) {
            return Response::download(
                $assets[0]->getFilename(),
                $assets[0]->getOriginalFilename()
            );
        } else {
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

    /**
     * Display the asset manager.
     */
    public function index()
    {
        return view($this->viewPrefix.'index');
    }

    public function get()
    {
        $params = $this->request->input();

        return [
            'total' => Helpers::countAssets($params),
            'html'  => view($this->viewPrefix.'thumbs', [
                'assets' => Helpers::getAssets($params),
            ])->render(),
        ];
    }

    public function picker()
    {
        return view($this->viewPrefix.'picker');
    }

    public function replace(Asset $asset)
    {
        list($validFiles, $errors) = $this->validateFileUpload();

        foreach ($validFiles as $file) {
            $asset->setType(AssetHelper::typeFromMimetype($file->getMimeType()));
            AssetFacade::save($asset);
            AssetFacade::createVersionFromFile($asset, $file);

            return [$asset->getId()];
        }

        if (count($errors)) {
            return new JsonResponse($errors, 500);
        }
    }

    public function removeSite(Asset $asset, Site $site)
    {
        $asset->removeSite($site);
    }

    public function revert(Asset $asset)
    {
        AssetFacade::revert($asset, $this->request->input('version_id'));
    }

    public function save(Asset $asset)
    {
        $asset
            ->setTitle($this->request->input('title'))
            ->setDescription($this->request->input('description'))
            ->setCredits($this->request->input('credits'))
            ->setThumbnailAssetId($this->request->input('thumbnail_asset_id'));

        AssetFacade::save($asset);
    }

    public function upload()
    {
        $assetIds = [];

        list($validFiles, $errors) = $this->validateFileUpload();

        foreach ($validFiles as $file) {
            $asset = new Asset();
            $asset
                ->setUploadedTime(new DateTime('now'))
                ->setUploadedBy(Auth::user())
                ->setTitle($file->getClientOriginalName())
                ->setType(AssetHelper::typeFromMimetype($file->getMimeType()));

            $assetIds[] = AssetFacade::save($asset)->getId();
            AssetFacade::createVersionFromFile($asset, $file);
        }

        return (count($errors)) ? new JsonResponse($errors, 500) : $assetIds;
    }

    protected function validateFileUpload()
    {
        $validFiles = $errors = [];

        foreach ($this->request->file() as $files) {
            foreach ($files as $i => $file) {
                if (!$file->isValid()) {
                    $errors[] = $file->getErrorMessage();
                    continue;
                }

                $type = AssetHelper::typeFromMimetype($file->getMimetype());

                if ($type === null) {
                    $errors[] = "File {$file->getClientOriginalName()} is of an unsuported type: {$file->getMimetype()}";
                    continue;
                }

                $validFiles[] = $file;
            }
        }

        return [$validFiles, $errors];
    }

    public function view(Asset $asset)
    {
        return view("$this->viewPrefix/view", [
            'asset' => $asset,
        ]);
    }
}
