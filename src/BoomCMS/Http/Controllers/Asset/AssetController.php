<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Core\Asset\Collection;
use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\Site;
use BoomCMS\Foundation\Http\ValidatesAssetUpload;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use BoomCMS\Support\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AssetController extends Controller
{
    use ValidatesAssetUpload;

    /**
     * Controller for handling a single file upload from TinyMCE.
     *
     * @see https://www.tinymce.com/docs/configure/file-image-upload/#automatic_uploads
     *
     * @param Request $request
     *
     * @return array
     */
    public function createFromBlob(Request $request, Site $site): array
    {
        $this->authorize('uploadAssets', $site);

        $file = $request->file('file');
        $error = $this->validateFile($file);

        if ($error !== true) {
            return [];
        }

        $description = trans('boomcms::asset.automatic-upload-description', [
            'url' => $request->server('HTTP_REFERER'),
        ]);

        $asset = AssetFacade::createFromFile($file);
        $asset->setDescription($description);

        AssetFacade::save($asset);

        (new Collection([$asset->getId()]))->addTag('Text editor uploads');

        return [
            'location' => route('asset', ['asset' => $asset]),
        ];
    }

    /**
     * @param Asset $asset
     */
    public function destroy(Asset $asset, Site $site)
    {
        $this->authorize('manageAssets', $site);

        AssetFacade::delete([$asset->getId()]);
    }

    /**
     * Download the given asset.
     *
     * @param Asset $asset
     *
     * @return Response
     */
    public function download(Asset $asset): Response
    {
        return new Response(AssetFacade::file($asset), 200, [
            'content-disposition' => 'download; filename="'.$asset->getOriginalFilename().'"',
        ]);
    }

    /**
     * Returns the HTML to embed the given asset.
     *
     * @param Request $request
     * @param Asset   $asset
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
            AssetFacade::replaceWith($asset, $file);

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
            $assetIds[] = AssetFacade::createFromFile($file)->getId();
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
