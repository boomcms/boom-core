<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Repositories\Asset as AssetRepositoryInterface;
use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Database\Models\Asset as AssetModel;
use BoomCMS\Database\Models\AssetVersion as AssetVersionModel;
use BoomCMS\Support\Facades\Auth;
use BoomCMS\Support\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Asset implements AssetRepositoryInterface
{
    /**
     * @var AssetModel
     */
    protected $model;

    /**
     * @var AssetVersion
     */
    protected $version;

    /**
     * @param AssetModel $model
     * @param AsserVersionModel $version
     */
    public function __construct(AssetModel $model, AssetVersionModel $version)
    {
        $this->model = $model;
        $this->version = $version;
    }

    /**
     * @param AssetInterface $asset
     * @param UploadedFile $file
     *
     * @return AssetVersion
     */
    public function createVersionFromFile(AssetInterface $asset, UploadedFile $file)
    {
        list($width, $height) = getimagesize($file->getRealPath());

        $extension = File::extension($file->getClientOriginalName(), $file->getMimetype());

        $version = $this->version->create([
            'asset_id'  => $asset->getId(),
            'extension' => $extension,
            'filesize'  => $file->getClientSize(),
            'filename'  => $file->getClientOriginalName(),
            'width'     => $width,
            'height'    => $height,
            'edited_at' => time(),
            'edited_by' => Auth::getPerson()->getId(),
            'mimetype'  => $file->getMimeType(),
            'metadata'  => File::exif($file->getRealPath()),
        ]);

        $file->move($asset->directory(), $version->id);

        return $version;
    }

    /**
     * @param array $assetIds
     *
     * @return $this
     */
    public function delete(array $assetIds)
    {
        $this->model->destroy($assetIds);

        return $this;
    }

    /**
     * @param int $id
     *
     * @return AssetModel
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * @param array $assetIds
     *
     * @return array
     */
    public function findMultiple(array $assetIds)
    {
        $assetIds = array_unique($assetIds);
        $assets = [];

        foreach ($assetIds as $assetId) {
            $asset = $this->find($assetId);

            if ($asset) {
                $assets[] = $asset;
            }
        }

        return $assets;
    }

    public function findByVersionId($versionId)
    {
        return $this->model->withVersion($versionId)->first();
    }

    public function revert(AssetInterface $asset, $versionId)
    {
        $version = $this->version->find($versionId);

        if ($version && $version->getAssetId() == $asset->getId()) {
            $attrs = $version->toArray();
            unset($attrs['id']);
            $attrs['edited_at'] = time();
            $attrs['edited_by'] = Auth::getPerson()->getId();

            $version = $this->version->create($attrs);

            copy(
                $asset->directory().DIRECTORY_SEPARATOR.$versionId,
                $asset->directory().DIRECTORY_SEPARATOR.$version->getId()
            );
        }

        return $asset;
    }

    /**
     * @param AssetModel $model
     *
     * @return AssetModel
     */
    public function save(AssetModel $model)
    {
        $model->save();

        return $model;
    }
}
