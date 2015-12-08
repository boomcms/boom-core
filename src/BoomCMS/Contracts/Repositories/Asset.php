<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Database\Models\Asset as AssetObject;
use BoomCMS\Database\Models\AssetVersion;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface Asset
{
    public function __construct(AssetObject $model, AssetVersion $version);

    /**
     * Add an asset version to an asset from an uploaded file.
     * 
     * @param AssetInterface $asset
     * @param UploadedFile   $file
     */
    public function createVersionFromFile(AssetInterface $asset, UploadedFile $file);

    /**
     * Retrive an asset by ID.
     * 
     * @param int $id
     */
    public function find($id);

    /**
     * Find assets by an array of asset IDs.
     *
     * @param array $assetIds
     *
     * @return array
     */
    public function findMultiple(array $assetIds);

    /**
     * Find the asset which is associated with a particular version ID.
     *
     * @param int $versionId
     */
    public function findByVersionID($versionId);

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
}
