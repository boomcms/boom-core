<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Contracts\Repositories\AssetVersion as AssetVersionRepositoryInterface;
use BoomCMS\Database\Models\Asset as AssetObject;
use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use Illuminate\Contracts\Filesystem\Factory as Filesystem;
use Illuminate\Support\Collection;

interface Asset extends Repository
{
    public function __construct(
        AssetObject $model,
        AssetVersionRepositoryInterface $version,
        Filesystem $filesystems
    );

    public function createFromFile(string $disk, FileInfoDriver $file): AssetInterface;

    /**
     * Returns the extensions which exist in the database.
     *
     * @return Collection
     */
    public function extensions(): Collection;

    /**
     * Retrieve an asset by ID.
     *
     * @param int $assetId
     */
    public function find($assetId);

    /**
     * Returns the path to the given asset.
     *
     * @param AssetInterface $asset
     */
    public function path(AssetInterface $asset): string;

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
