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
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

    /**
     * Download the given asset
     *
     * @param Asset $asset
     *
     * @return BinaryFileResponse
     */
    public function download(Asset $asset): BinaryFileResponse
    {
        return response()->download(
            $asset->getFilename(),
            $asset->getOriginalFilename()
        );
    }

    /**
     * Returns the HTML to embed the given asset
     *
     * @param Request $request
     * @param Asset $asset
     *
     * @return View
     */
    public function embed(Request $request, Asset $asset): View
    {
        $viewPrefix = 'boomcms::assets.embed.';
        $assetType = strtolower(class_basename($asset->getType()));
        $viewName = $viewPrefix.$assetType;

        if (!view()->exists($viewName)) {
            $viewName = $viewPrefix.'default';
        }

        return view()->make($viewName, [
            'asset'  => $asset,
            'height' => $request->input('height'),
            'width'  => $request->input('width'),
        ]);
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
            $assetIds[] = AssetFacade::createFromFile($file);
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
            Asset::ATTR_CREDITS, Asset::ATTR_THUMBNAIL_ID, Asset::ATTR_PUBLIC, ];

        $asset
            ->fill($request->only($fields))
            ->setPublishedAt($request->input(Asset::ATTR_PUBLISHED_AT));

        AssetFacade::save($asset);

        return $this->show($asset, $site);
    }
}
