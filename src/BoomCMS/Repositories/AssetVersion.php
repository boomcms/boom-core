<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Database\Models\AssetVersion as AssetVersionModel;
use BoomCMS\Contracts\Repositories\AssetVersion as AssetVersionRepositoryInterface;
use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use BoomCMS\FileInfo\Facade as FileInfo;
use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AssetVersion implements AssetVersionRepositoryInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var AssetVersionModel
     */
    protected $model;

    /**
     * @param AssetVersionModel $model
     */
    public function __construct(AssetVersionModel $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     *
     * @param AssetInterface $asset
     * @param UploadedFile   $file
     * @param FileInfoDriver $info
     *
     * @return AssetVersionModel
     */
    public function createFromFile(
        AssetInterface $asset,
        UploadedFile $file,
        FileInfoDriver $info = null
    ): AssetVersionModel
    {
        $info = $info ?: FileInfo::create($file);

        $version = $this->model->create([
            AssetVersionModel::ATTR_ASPECT_RATIO => $info->getAspectRatio(),
            AssetVersionModel::ATTR_WIDTH        => $info->getWidth(),
            AssetVersionModel::ATTR_HEIGHT       => $info->getHeight(),
            AssetVersionModel::ATTR_METADATA     => $info->getMetadata(),
            AssetVersionModel::ATTR_EXTENSION    => $file->guessExtension(),
            AssetVersionModel::ATTR_FILESIZE     => $file->getClientSize(),
            AssetVersionModel::ATTR_FILENAME     => $file->getClientOriginalName(),
            AssetVersionModel::ATTR_MIME         => $file->getMimeType(),
            AssetVersionModel::ATTR_ASSET        => $asset->getId(),
        ]);

        $asset->setVersion($version);

        return $version;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $versionId
     *
     * @return AssetVersionModel
     */
    public function find($versionId): AssetVersionModel
    {
        return $this->model->find($versionId);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $versionId
     *
     * @return AssetInterface
     */
    public function findAssetByVersionId($versionId): AssetInterface
    {
        $version = $this->findVersion($versionId);
        $asset = $version->getAsset();
        $asset->setVersion($version);

        return $asset;
    }
}
