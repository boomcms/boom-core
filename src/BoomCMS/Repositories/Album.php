<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Album as AlbumInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Contracts\Repositories\Album as AlbumRepositoryInterface;
use BoomCMS\Database\Models\Album as Model;
use BoomCMS\Foundation\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Album extends Repository implements AlbumRepositoryInterface
{
    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * @param Model         $model
     * @param SiteInterface $site
     */
    public function __construct(Model $model, SiteInterface $site = null)
    {
        $this->model = $model;
        $this->site = $site;
    }

    /**
     * {@inheritdoc}
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model
            ->whereSiteIs($this->site)
            ->orderBy(Model::ATTR_NAME, 'asc')
            ->get();
    }

    /**
     * {@inheritdoc}
     *
     * @param string      $name
     * @param string|null $description
     *
     * @return AlbumInterface
     */
    public function create($name, $description = null): AlbumInterface
    {
        return $this->model
            ->create([
                Model::ATTR_NAME        => $name,
                Model::ATTR_DESCRIPTION => $description,
            ]);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name
     *
     * @return null|AlbumInterface
     */
    public function findByName($name)
    {
        return $this->model->where(Model::ATTR_NAME, $name)->first();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name
     *
     * @return AlbumInterface
     */
    public function findOrCreate($name): AlbumInterface
    {
        $album = $this->findByName($name);

        return ($album === null) ? $this->create($name) : $album;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $assetIds
     *
     * @return Collection
     */
    public function findByAssetIds(array $assetIds): Collection
    {
        return $this->model
            ->withCount(['assets' => function (Builder $query) use ($assetIds) {
                $query->whereIn('asset_id', $assetIds);
            }])
            ->having('assets_count', '=', count($assetIds))
            ->get();
    }

    public function removeAssetsFromCounts(array $assetIds)
    {
        $subquery = DB::table('albums')
            ->select('albums.id', DB::raw('count(*) as c'))
            ->leftJoin('album_asset', 'albums.id', '=', 'album_id')
            ->leftJoin('assets', 'asset_id', '=', 'assets.id')
            ->whereNotIn('assets.id', $assetIds)
            ->groupBy('albums.id');

        $this->model
            ->newQuery()
            ->join(DB::raw('('.$subquery->toSql().') as sq'), 'albums.id', '=', 'sq.id')
            ->mergeBindings($subquery)
            ->join('album_asset', 'albums.id', '=', 'album_id')
            ->whereIn('asset_id', $assetIds)
            ->update([
                Model::ATTR_ASSET_COUNT => DB::raw('sq.c'),
            ]);
    }
}
