<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Database\Models\AssetVersion as AssetVersionModel;
use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface AssetVersion
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

    /**
     * Create an asset version from an uploaded file.
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
    ): AssetVersionModel;

    /**
     * Find an asset version by it's ID.
     *
     * @param int $versionId
     *
     * @return AssetVersionModel
     */
    public function find($versionId): AssetVersionModel;

    /**
     * Find the asset which has the version with the given ID.
     *
     * @param int $versionId
     *
     * @return AssetInterface
     */
    public function findAssetByVersionId($versionId): AssetInterface;
}
