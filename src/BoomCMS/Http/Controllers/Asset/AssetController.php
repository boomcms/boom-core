<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Foundation\Http\ValidatesAssetUpload;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use BoomCMS\Support\Helpers;
use BoomCMS\Support\Helpers\Asset as AssetHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    use ValidatesAssetUpload;

    protected $role = 'manageAssets';

    public function destroy(Request $request, Asset $asset = null)
    {
        $assetIds = ($asset->getId()) ? [$asset->getId()] : $request->input('assets');

        AssetFacade::delete($assetIds);
    }

    // Needs to not require the manageAssets role. Move to another controller.
    public function index(Request $request)
    {
        return [
            'total'  => Helpers::countAssets($request->input()),
            'assets' => Helpers::getAssets($request->input()),
        ];
    }

    /**
     * @param Request $request
     * @param Asset   $asset
     *
     * @return JsonResponse
     */
    public function replace(Request $request, Asset $asset)
    {
        list($validFiles, $errors) = $this->validateAssetUpload($request);

        foreach ($validFiles as $file) {
            $asset->setType(AssetHelper::typeFromMimetype($file->getMimeType()));

            AssetFacade::save($asset);
            AssetFacade::createVersionFromFile($asset, $file);

            return $this->show($asset);
        }

        if (count($errors)) {
            return new JsonResponse($errors, 500);
        }
    }

    /**
     * @param Request $request
     * @param Asset   $asset
     */
    public function revert(Request $request, Asset $asset)
    {
        AssetFacade::revert($asset, $request->input('version_id'));

        return $this->show($asset);
    }

    /**
     * @param Asset $asset
     */
    public function show(Asset $asset)
    {
        return $asset
            ->newQuery()
            ->with('versions')
            ->with('versions.editedBy')
            ->with('uploadedBy')
            ->find($asset->getId());
    }

    /**
     * @return array
     */
    public function tags(Asset $asset)
    {
        return $asset->getTags();
    }

    /**
     * @param Request $request
     * @param Asset   $asset
     */
    public function update(Request $request, Asset $asset)
    {
        $asset
            ->setTitle($request->input(Asset::ATTR_TITLE))
            ->setDescription($request->input(Asset::ATTR_DESCRIPTION))
            ->setCredits($request->input(Asset::ATTR_CREDITS))
            ->setThumbnailAssetId($request->input(Asset::ATTR_THUMBNAIL_ID));

        AssetFacade::save($asset);

        return $this->show($asset);
    }
}