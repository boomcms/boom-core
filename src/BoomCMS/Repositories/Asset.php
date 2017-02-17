<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Contracts\Repositories\Asset as AssetRepositoryInterface;
use BoomCMS\Contracts\Repositories\AssetVersion as AssetVersionRepositoryInterface;
use BoomCMS\Database\Models\Asset as AssetModel;
use BoomCMS\Database\Models\AssetVersion as AssetVersionModel;
use BoomCMS\Database\Models\Person as PersonModel;
use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use BoomCMS\FileInfo\Facade as FileInfo;
use BoomCMS\Support\Helpers\Asset as AssetHelper;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Asset implements AssetRepositoryInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var AssetModel
     */
    protected $model;

    /**
     * @var AssetVersionRepositoryInterface
     */
    protected $version;

    /**
     * @param AssetModel        $model
     * @param AsserVersionModel $version
     */
    public function __construct(AssetModel $model, AssetVersionRepositoryInterface $version, Filesystem $filesystem)
    {
        $this->model = $model;
        $this->version = $version;
        $this->filesystem = $filesystem;
    }

    /**
     * Create an asset from an uploaded file, setting default values.
     *
     * @param UploadedFile $file
     *
     * @return int
     */
    public function createFromFile(UploadedFile $file): int
    {
        $info = FileInfo::create($file);

        $asset = new AssetModel();
        $asset
            ->setTitle($info->getTitle() ?: $file->getClientOriginalName())
            ->setPublishedAt($info->getCreatedAt())
            ->setType(AssetHelper::typeFromMimetype($file->getMimeType()))
            ->setDescription($info->getDescription())
            ->setCredits($info->getCopyright());

        $assetId = static::save($asset)->getId();

        if ($thumbnail = $info->getThumbnail()) {
            $this->filesystem->put("$assetId.thumb", $thumbnail->getImageBlob());
        }

        $this->version->createFromFile($asset, $file, $info);

        return $assetId;
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
        $version = $this->find($versionId);

        if ($version && $version->getAssetId() == $asset->getId()) {
            $attrs = $version->toArray();
            unset($attrs['id']);

            $version = $this->model->create($attrs);

            $this->filesystem->copy($versionId, $version->getId());
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
            ->join('assets', 'assets.'.AssetModel::ATTR_CREATED_BY, '=', 'people.id')
            ->groupBy('people.id')
            ->orderBy(PersonModel::ATTR_NAME)
            ->get();
    }
}
