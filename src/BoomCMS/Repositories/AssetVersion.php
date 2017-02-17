<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Database\Models\AssetVersion as AssetVersionModel;
use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use BoomCMS\FileInfo\Facade as FileInfo;
use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AssetVersion
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
     * @param AsserVersionModel $model
     */
    public function __construct(AssetVersionModel $model, Filesystem $filesystem)
    {
        $this->model = $model;
        $this->filesystem = $filesystem;
    }

    /**
     * @param AssetInterface $asset
     * @param UploadedFile   $file
     * @param FileInfoDriver $info
     *
     * @return AssetVersion
     */
    public function createFromFile(AssetInterface $asset, UploadedFile $file, FileInfoDriver $info = null)
    {
        $info = $info ?: FileInfo::create($file);

        $version = $this->model->create([
            AssetVersionModel::ATTR_ASPECT_RATIO => $info->getAspectRatio(),
            AssetVersionModel::ATTR_ASSET        => $asset->getId(),
            AssetVersionModel::ATTR_EXTENSION    => $file->guessExtension(),
            AssetVersionModel::ATTR_FILESIZE     => $file->getClientSize(),
            AssetVersionModel::ATTR_FILENAME     => $file->getClientOriginalName(),
            AssetVersionModel::ATTR_WIDTH        => $info->getWidth(),
            AssetVersionModel::ATTR_HEIGHT       => $info->getHeight(),
            AssetVersionModel::ATTR_MIME         => $file->getMimeType(),
            AssetVersionModel::ATTR_METADATA     => $info->getMetadata(),
        ]);

        $this->filesystem->putFileAs(null, $file, $version->id);

        $asset->setVersion($version);

        return $version;
    }

    public function extensions()
    {
        return $this->model
            ->select(AssetVersionModel::ATTR_EXTENSION)
            ->where(AssetVersionModel::ATTR_EXTENSION, '!=', '')
            ->orderBy(AssetVersionModel::ATTR_EXTENSION)
            ->distinct()
            ->pluck(AssetVersionModel::ATTR_EXTENSION);
    }

    public function find($versionId)
    {
        return $this->model->find($versionId);
    }

    public function findAssetByVersionId($versionId)
    {
        $version = $this->findVersion($versionId);
        $asset = $version->getAsset();
        $asset->setVersion($version);

        return $asset;
    }
}
