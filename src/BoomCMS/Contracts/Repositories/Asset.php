<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Contracts\Repositories\AssetVersion as AssetVersionRepositoryInterface;
use BoomCMS\Database\Models\Asset as AssetObject;
use BoomCMS\Database\Models\Person as PersonModel;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Collection;
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
     * @return int
     */
    public function createFromFile(UploadedFile $file): int;

    /**
     * Returns the extensions which exist in the database.
     *
     * @return array
     */
    public function extensions(): array;

    /**
     * Retrieve an asset by ID.
     *
     * @param int $assetId
     */
    public function find($assetId);

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
     * Returns the users who have uploaded assets.
     *
     * @return Collection
     */
    public function uploaders(PersonModel $model = null);
}
