<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Album as AlbumInterface;
use Illuminate\Support\Collection;

interface Album extends Repository
{
    /**
     * Returns a collection containing all albums.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Create an album with the given name and description.
     *
     * @param string      $name
     * @param string|null $description
     *
     * @return AlbumInterace
     */
    public function create($name, $description = null): AlbumInterface;

    /**
     * Find an album by name
     *
     * @param string $name
     *
     * @return null|AlbumInterace
     */
    public function findByName($name);

    /**
     * Find all the albums which include the given asset IDs.
     *
     * @param array $assetIds
     *
     * @return Collection
     */
    public function findByAssetIds(array $assetIds): Collection;
}
