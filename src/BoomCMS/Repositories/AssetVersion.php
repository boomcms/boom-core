<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Contracts\Repositories\AssetVersion as AssetVersionRepositoryInterface;
use BoomCMS\Database\Models\AssetVersion as AssetVersionModel;
use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use BoomCMS\Foundation\Repository;
use Illuminate\Contracts\Filesystem\Filesystem;

class AssetVersion extends Repository implements AssetVersionRepositoryInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

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
     * @param array $attrs
     *
     * @return AssetVersionModel
     */
    public function create(array $attrs): AssetVersionModel
    {
        return $this->model->create($attrs);
    }

    public function createFromFile(
        AssetInterface $asset,
        string $disk,
        FileInfoDriver $info
    ): AssetVersionModel {
        $version = $this->create([
            AssetVersionModel::ATTR_ASPECT_RATIO => $info->getAspectRatio(),
            AssetVersionModel::ATTR_WIDTH        => $info->getWidth(),
            AssetVersionModel::ATTR_HEIGHT       => $info->getHeight(),
            AssetVersionModel::ATTR_METADATA     => $info->getMetadata(),
            AssetVersionModel::ATTR_EXTENSION    => $info->getExtension(),
            AssetVersionModel::ATTR_FILESIZE     => $info->getFilesize(),
            AssetVersionModel::ATTR_FILENAME     => $info->getFilename(),
            AssetVersionModel::ATTR_MIME         => $info->getMimetype(),
            AssetVersionModel::ATTR_PATH         => $info->getPath(),
            AssetVersionModel::ATTR_ASSET        => $asset->getId(),
            AssetVersionModel::ATTR_FILESYSTEM   => $disk,
        ]);

        $asset->setVersion($version);

        return $version;
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
        $version = $this->find($versionId);
        $asset = $version->getAsset();
        $asset->setVersion($version);

        return $asset;
    }

    public function existsByFilesystemAndPath(string $disk, string $path): bool
    {
        return $this->model
            ->where(AssetVersionModel::ATTR_FILESYSTEM, $disk)
            ->where(AssetVersionModel::ATTR_PATH, $path)
            ->exists();
    }
}
