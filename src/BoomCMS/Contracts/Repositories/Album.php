<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Album as AlbumInterface;
use Illuminate\Support\Collection;

interface Album
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
     * Delete the given album.
     *
     * @param AlbumInterface $album
     */
    public function delete(AlbumInterface $album);

    /**
     * Returns the album with the given ID.
     *
     * @param int|array $albumId
     *
     * @return null|AlbumInterface
     */
    public function find($albumId);

    /**
     * Find all the albums which include the given asset IDs.
     *
     * @param array $assetIds
     *
     * @return Collection
     */
    public function findByAssetIds(array $assetIds): Collection;

    /**
     * Save the given album.
     *
     * @param AlbumInterface $album
     *
     * @reutnr AlbumInterface
     */
    public function save(AlbumInterface $album): AlbumInterface;
}
