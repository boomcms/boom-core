<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Album as AlbumInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Database\Models\Album as Model;
use BoomCMS\Foundation\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Album extends Repository
{
    /**
     * @var SiteInterface
     */
    protected $site;

    public function __construct(Model $model, SiteInterface $site = null)
    {
        $this->model = $model;
        $this->site = $site;
    }

    public function all(): Collection
    {
        return $this->model
            ->whereSiteIs($this->site)
            ->orderBy(Model::ATTR_NAME, 'asc')
            ->get();
    }

    public function create($name, $description = null): AlbumInterface
    {
        return $this->model
            ->create([
                Model::ATTR_NAME        => $name,
                Model::ATTR_DESCRIPTION => $description,
            ]);
    }

    public function findByName($name)
    {
        return $this->model->where(Model::ATTR_NAME, $name)->first();
    }

    public function findOrCreate($name): AlbumInterface
    {
        $album = $this->findByName($name);

        return ($album === null) ? $this->create($name) : $album;
    }

    public function findByAssetIds(array $assetIds): Collection
    {
        return $this->model
            ->withCount(['assets' => function (Builder $query) use ($assetIds) {
                $query->whereIn('asset_id', $assetIds);
            }])
            ->having('assets_count', '=', count($assetIds))
            ->get();
    }
}
