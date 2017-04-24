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
     * Returns the album with the given ID.
     *
     * @param int|array $albumId
     *
     * @return null|AlbumInterface
     */
    public function find($albumId);
}
