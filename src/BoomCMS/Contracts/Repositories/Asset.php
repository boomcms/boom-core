<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Database\Models\Asset as AssetObject;

interface Asset
{
    public function __construct(AssetObject $model);

    /**
     * Retrive an asset by ID
     * 
     * @param int $id
     */
    public function find($id);

    /**
     * Find the asset which is associated with a particular version ID
     *
     * @param int $versionId
     */
    public function findByVersionID($versionId);

    /**
     * Save an asset
     *
     * @param AssetObject $asset
     */
    public function save(AssetObject $asset);
}
