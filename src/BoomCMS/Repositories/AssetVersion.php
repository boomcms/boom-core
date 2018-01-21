<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Database\Models\AssetVersion as AssetVersionModel;
use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use BoomCMS\FileInfo\Facade as FileInfo;
use BoomCMS\Foundation\Repository;
use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AssetVersion extends Repository
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(AssetVersionModel $model)
    {
        $this->model = $model;
    }

    public function create(array $attrs): AssetVersionModel
    {
        return $this->model->create($attrs);
    }

    public function createFromFile(
        AssetInterface $asset,
        UploadedFile $file,
        FileInfoDriver $info = null
    ): AssetVersionModel {
        $info = $info ?: FileInfo::create($file);

        $version = $this->create([
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

    public function findAssetByVersionId($versionId): AssetInterface
    {
        $version = $this->find($versionId);
        $asset = $version->getAsset();
        $asset->setVersion($version);

        return $asset;
    }
}
