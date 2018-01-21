<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Contracts\Models\URL as URLInterface;
use BoomCMS\Database\Models\URL as Model;
use BoomCMS\Foundation\Repository;
use BoomCMS\Support\Helpers\URL as URLHelper;

class URL extends Repository
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

    public function create($location, PageInterface $page, $isPrimary = false): URLInterface
    {
        $unique = URLHelper::makeUnique(URLHelper::sanitise($location));

        return $this->model->create([
            Model::ATTR_LOCATION   => $unique,
            Model::ATTR_PAGE_ID    => $page->getId(),
            Model::ATTR_IS_PRIMARY => $isPrimary,
        ]);
    }

    public function findByLocation($location)
    {
        return $this->model
            ->where(Model::ATTR_SITE, '=', $this->site->getId())
            ->where(Model::ATTR_LOCATION, '=', URLHelper::sanitise($location))
            ->first();
    }

    public function isAvailable($path): bool
    {
        return !$this->model
            ->where(Model::ATTR_SITE, '=', $this->site->getId())
            ->where(Model::ATTR_LOCATION, '=', $path)
            ->exists();
    }

    public function page(PageInterface $page)
    {
        return $this->model
            ->where(Model::ATTR_PAGE_ID, '=', $page->getId())
            ->where(Model::ATTR_IS_PRIMARY, '=', true)
            ->first();
    }
}
