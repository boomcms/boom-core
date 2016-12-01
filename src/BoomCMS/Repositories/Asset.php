<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Contracts\Repositories\Asset as AssetRepositoryInterface;
use BoomCMS\Database\Models\Asset as AssetModel;
use BoomCMS\Database\Models\AssetVersion as AssetVersionModel;
use BoomCMS\Database\Models\Person as PersonModel;
use BoomCMS\Support\File;
use Illuminate\Database\Eloquent\Collection;
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
     * @param AssetModel        $model
     * @param AsserVersionModel $version
     */
    public function __construct(AssetModel $model, AssetVersionModel $version)
    {
        $this->model = $model;
        $this->version = $version;
    }

    /**
     * @param AssetInterface $asset
     * @param UploadedFile   $file
     *
     * @return AssetVersion
     */
    public function createVersionFromFile(AssetInterface $asset, UploadedFile $file)
    {
        list($width, $height) = getimagesize($file->getRealPath());

        $extension = File::extension($file->getClientOriginalName(), $file->getMimetype());

        $version = $this->version->create([
            'asset_id'   => $asset->getId(),
            'extension'  => $extension,
            'filesize'   => $file->getClientSize(),
            'filename'   => $file->getClientOriginalName(),
            'width'      => $width,
            'height'     => $height,
            'mimetype'   => $file->getMimeType(),
            'metadata'   => File::exif($file->getRealPath()),
        ]);

        $file->move($asset->directory(), $version->id);

        $asset->setVersion($version);

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

    public function extensions()
    {
        return $this->version
            ->select(AssetVersionModel::ATTR_EXTENSION)
            ->where(AssetVersionModel::ATTR_EXTENSION, '!=', '')
            ->orderBy(AssetVersionModel::ATTR_EXTENSION)
            ->distinct()
            ->pluck(AssetVersionModel::ATTR_EXTENSION);
    }

    /**
     * @param int $assetId
     *
     * @return AssetModel
     */
    public function find($assetId)
    {
        return $this->model->find($assetId);
    }

    public function findByVersionId($versionId)
    {
        $version = $this->findVersion($versionId);
        $asset = $version->getAsset();
        $asset->setVersion($version);

        return $asset;
    }

    public function findVersion($versionId)
    {
        return $this->version->find($versionId);
    }

    public function revert(AssetInterface $asset, $versionId)
    {
        $version = $this->version->find($versionId);

        if ($version && $version->getAssetId() == $asset->getId()) {
            $attrs = $version->toArray();
            unset($attrs['id']);

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

    /**
     * Returns a Collection of People who have uploaded assets.
     *
     * @return Collection
     */
    public function uploaders(PersonModel $model = null)
    {
        $model = $model ?: new PersonModel();

        return $model
            ->select('people.*')
            ->join('assets', AssetModel::ATTR_UPLOADED_BY, '=', 'people.id')
            ->groupBy('people.id')
            ->orderBy(PersonModel::ATTR_NAME)
            ->get();
    }
}
