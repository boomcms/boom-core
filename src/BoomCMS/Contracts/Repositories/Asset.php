<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Contracts\Repositories\AssetVersion as AssetVersionRepositoryInterface;
use BoomCMS\Database\Models\Asset as AssetObject;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface Asset
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
     * Save an asset.
     *
     * @param AssetObject $asset
     */
    public function save(AssetObject $asset);

    /**
     * Returns a read stream for the given asset.
     *
     * @param AssetObject $asset
     */
    public function stream(AssetInterface $asset);
}
