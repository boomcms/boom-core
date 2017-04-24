<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Contracts\Models\Album as AlbumInterface;
use BoomCMS\Contracts\Repositories\Album as AlbumRepositoryInterface;
use BoomCMS\Database\Models\Album as Model;
use Illuminate\Support\Collection;

class Album implements AlbumRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

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
//            ->whereSite($this->site)
            ->orderBy(Model::ATTR_NAME, 'asc')
            ->get();
    }

    /**
     * Returns the tag with the given ID.
     *
     * @param int $albumId
     *
     * @return null|AlbumInterface
     */
    public function find($albumId)
    {
        return $this->model->find($albumId);
    }
}
