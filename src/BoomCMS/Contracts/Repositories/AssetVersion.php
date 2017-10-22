<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Database\Models\AssetVersion as AssetVersionModel;
use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface AssetVersion extends Repository
{
    public function __construct(AssetVersionModel $model);

    /**
     * Create a version with the given attributes.
     *
     * @param array $attrs
     *
     * @return AssetVersionModel
     */
    public function create(array $attrs): AssetVersionModel;

    public function createFromFile(
        AssetInterface $asset,
        string $disk,
        FileInfoDriver $info
    ): AssetVersionModel;

    public function existsByFilesystemAndPath(string $disk, string $path): bool;

    /**
     * Find the asset which has the version with the given ID.
     *
     * @param int $versionId
     *
     * @return AssetInterface
     */
    public function findAssetByVersionId($versionId): AssetInterface;
}
