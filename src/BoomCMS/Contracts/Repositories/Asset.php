<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Contracts\Repositories\AssetVersion as AssetVersionRepositoryInterface;
use BoomCMS\Database\Models\Asset as AssetObject;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface Asset extends Repository
{
    public function __construct(
        AssetObject $model,
        AssetVersionRepositoryInterface $version,
        Filesystem $filesystem
    );

    /**
     * Create an asset from an uploaded file.
     *
     * @param UploadedFile $file
     *
     * @return AssetInterface
     */
    public function createFromFile(UploadedFile $file): AssetInterface;

    /**
     * Returns the extensions which exist in the database.
     *
     * @return Collection
     */
    public function extensions(): Collection;

    /**
     * Revert an asset to a previous version ID.
     *
     * @param AssetInterface $asset
     * @param int            $versionId
     */
    public function revert(AssetInterface $asset, $versionId);

    /**
     * Returns a read stream for the given asset.
     *
     * @param AssetObject $asset
     */
    public function stream(AssetInterface $asset);
}
