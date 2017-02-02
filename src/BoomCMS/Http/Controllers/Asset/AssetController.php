<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\Site;
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

    /**
     * @param Asset $asset
     */
    public function destroy(Asset $asset, Site $site)
    {
        $this->authorize('manageAssets', $site);

        AssetFacade::delete([$asset->getId()]);
    }

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
    public function replace(Request $request, Asset $asset, Site $site)
    {
        $this->authorize('manageAssets', $site);

        list($validFiles, $errors) = $this->validateAssetUpload($request);

        foreach ($validFiles as $file) {
            $asset->setType(AssetHelper::typeFromMimetype($file->getMimeType()));

            AssetFacade::save($asset);
            AssetFacade::createVersionFromFile($asset, $file);

            return $this->show($asset, $site);
        }

        if (count($errors)) {
            return new JsonResponse($errors, 500);
        }
    }

    /**
     * @param Request $request
     * @param Asset   $asset
     */
    public function revert(Request $request, Asset $asset, Site $site)
    {
        $this->authorize('manageAssets', $site);

        AssetFacade::revert($asset, $request->input('version_id'));

        return $this->show($asset, $site);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse|array
     */
    public function store(Request $request, Site $site)
    {
        $this->authorize('uploadAssets', $site);

        $assetIds = [];

        list($validFiles, $errors) = $this->validateAssetUpload($request);

        foreach ($validFiles as $file) {
            $asset = new Asset();
            $asset
                ->setTitle($file->getClientOriginalName())
                ->setType(AssetHelper::typeFromMimetype($file->getMimeType()));

            $assetIds[] = AssetFacade::save($asset)->getId();
            AssetFacade::createVersionFromFile($asset, $file);
        }

        return (count($errors)) ? new JsonResponse($errors, 500) : $assetIds;
    }

    /**
     * @param Asset $asset
     */
    public function show(Asset $asset, Site $site)
    {
        $this->authorize('manageAssets', $site);

        return $asset
            ->newQuery()
            ->with('versions')
            ->with('versions.editedBy')
            ->with('uploadedBy')
            ->find($asset->getId());
    }

    /**
     * @param Request $request
     * @param Asset   $asset
     */
    public function update(Request $request, Asset $asset, Site $site)
    {
        $this->authorize('manageAssets', $site);

        $fields = [Asset::ATTR_TITLE, Asset::ATTR_DESCRIPTION,
            Asset::ATTR_CREDITS, Asset::ATTR_THUMBNAIL_ID, Asset::ATTR_PUBLIC];

        $asset
            ->fill($request->only($fields))
            ->setPublishedAt($request->input(Asset::ATTR_PUBLISHED_AT));

        AssetFacade::save($asset);

        return $this->show($asset, $site);
    }
}
