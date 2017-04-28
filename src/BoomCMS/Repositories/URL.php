<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Contracts\Models\URL as URLInterface;
use BoomCMS\Contracts\Repositories\URL as URLRepositoryInterface;
use BoomCMS\Database\Models\URL as Model;
use BoomCMS\Support\Helpers\URL as URLHelper;

class URL implements URLRepositoryInterface
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
     * @param Model        $model
     * @param SiteInteface $site
     */
    public function __construct(Model $model, SiteInterface $site = null)
    {
        $this->model = $model;
        $this->site = $site;
    }

    /**
     * @param string        $location
     * @param PageInterface $page
     * @param bool          $isPrimary
     *
     * @return URLInterface
     */
    public function create($location, PageInterface $page, $isPrimary = false)
    {
        $unique = URLHelper::makeUnique(URLHelper::sanitise($location));

        return $this->model->create([
            Model::ATTR_LOCATION   => $unique,
            Model::ATTR_PAGE_ID    => $page->getId(),
            Model::ATTR_IS_PRIMARY => $isPrimary,
        ]);
    }

    /**
     * @param URLInterface $url
     *
     * @return $this
     */
    public function delete(URLInterface $url)
    {
        $url->delete();

        return $this;
    }

    /**
     * Returns the URL with the given ID.
     *
     * @param int $urlId
     *
     * @return null|URLInterface
     */
    public function find($urlId)
    {
        return $this->model->find($urlId);
    }

    /**
     * @param string $location
     *
     * @return URLInterface
     */
    public function findByLocation($location)
    {
        return $this->model
            ->where(Model::ATTR_SITE, '=', $this->site->getId())
            ->where(Model::ATTR_LOCATION, '=', URLHelper::sanitise($location))
            ->first();
    }

    /**
     * Determine whether a URL is already being used by a page in the CMS.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isAvailable($path)
    {
        return !$this->model
            ->where(Model::ATTR_SITE, '=', $this->site->getId())
            ->where(Model::ATTR_LOCATION, '=', $path)
            ->exists();
    }

    /**
     * Returns the primary URL for the given page.
     *
     * @param PageInterface $page
     *
     * @return URLInterface
     */
    public function page(PageInterface $page)
    {
        return $this->model
            ->where(Model::ATTR_PAGE_ID, '=', $page->getId())
            ->where(Model::ATTR_IS_PRIMARY, '=', true)
            ->first();
    }

    /**
     * @param URLInterface $url
     *
     * @return URLInterface
     */
    public function save(URLInterface $url): URLInterface
    {
        $url->save();

        return $url;
    }
}
