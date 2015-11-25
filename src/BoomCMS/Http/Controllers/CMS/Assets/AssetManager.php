<?php

namespace BoomCMS\Http\Controllers\CMS\Assets;

use BoomCMS\Core\Asset\Query;
use BoomCMS\Database\Models\Asset;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use BoomCMS\Support\Facades\Auth;
use BoomCMS\Support\Helpers\Asset as AssetHelper;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
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
            $this->authorization('manage_assets');
        }
    }

    public function delete()
    {
        AssetFacade::delete($this->request->input('assets'));
    }

    public function download()
    {
        $assetIds = array_unique((array) $this->request->input('asset'));
        $assets = [];

        foreach ($assetIds as $assetId) {
            $asset = AssetFacade::find($assetId);

            if ($asset) {
                $assets[] = $asset;
            }
        }

        if (count($assets) === 1) {
            return Response::download(
                $assets[0]->getFilename(),
                $assets[0]->getOriginalFilename()
            );
        } else {
            $filename = tempnam(sys_get_temp_dir(), 'boomcms_asset_download');
            $zip = new ZipArchive();
            $zip->open($filename, ZipArchive::CREATE);

            foreach ($assets as $asset) {
                $zip->addFile($asset->getFilename(), $asset->getOriginalFilename());
            }

            $zip->close();

            $response = Response::make()
                ->header('Content-type', 'application/zip')
                ->header('Content-Disposition', 'attachment; filename=cms_assets.zip')
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
        return View::make($this->viewPrefix.'index', [
            'manager' => $this->manager(),
            'person'  => $this->person,
        ]);
    }

    public function get()
    {
        $defaults = [
            'page'  => 1,
            'limit' => 30,
            'order' => 'last_modified desc',
        ];

        $params = $this->request->input() + $defaults;

        $query = new Query($params);
        $count = $query->count();

        if ($count === 0) {
            return View::make($this->viewPrefix.'none_found');
        } else {
            return View::make($this->viewPrefix.'list', [
                'assets' => $query->getResults(),
                'total'  => $count,
                'pages'  => ceil($count / $params['limit']),
                'page'   => $this->request->input('page'),
            ]);
        }
    }

    /**
     * Display the asset manager without topbar etc.
     */
    public function manager()
    {
        return View::make($this->viewPrefix.'manager');
    }

    public function picker()
    {
        return View::make($this->viewPrefix.'picker');
    }

    public function replace(Asset $asset)
    {
        list($validFiles, $errors) = $this->validateFileUpload();

        foreach ($validFiles as $file) {
            $asset->createVersionFromFile($file);
            AssetFacade::save($asset);

            return [$asset->getId()];
        }

        if (count($errors)) {
            return new JsonResponse($errors, 500);
        }
    }

    public function revert(Asset $asset)
    {
        $asset->revertTo($this->request->input('version_id'));
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
                ->setUploadedBy(Auth::getPerson());

            $assetIds[] = AssetFacade::save($asset)->getId();
            $asset->createVersionFromFile($file);
            AssetFacade::save($asset);
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
        return View::make("$this->viewPrefix/view", [
            'asset' => $asset,
        ]);
    }
}
