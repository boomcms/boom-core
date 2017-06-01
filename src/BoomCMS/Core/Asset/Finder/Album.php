<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Contracts\Models\Album as AlbumContract;
use BoomCMS\Foundation\Finder\Filter as BaseFilter;
use BoomCMS\Support\Facades\Album as AlbumFacade;
use Illuminate\Database\Eloquent\Builder;

class Album extends BaseFilter
{
    /**
     * @var AlbumContract
     */
    protected $album;

    /**
     * @param AlbumContract|int $album
     */
    public function __construct(...$albums)
    {
        $this->album = (count($albums) === 1 && $albums[0] instanceof AlbumContract) ?
            $albums : AlbumFacade::find($albums);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function build(Builder $query)
    {
        return $query->whereAlbum($this->album);
    }

    /**
     * @return bool
     */
    public function shouldBeApplied(): bool
    {
        return $this->album !== null;
    }
}
