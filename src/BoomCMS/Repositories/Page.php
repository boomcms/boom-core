<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Page as PageModelInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Contracts\Repositories\Page as PageRepositoryInterface;
use BoomCMS\Database\Models\Page as Model;
use BoomCMS\Page\Finder;
use BoomCMS\Support\Facades\Router;
use Illuminate\Database\Eloquent\Collection;

class Page implements PageRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $attrs = [])
    {
        return Model::create($attrs);
    }

    public function delete(PageModelInterface $page)
    {
        $page->delete();

        return $this;
    }

    /**
     * Returns a page with the given ID.
     *
     * @param int $pageId
     *
     * @return null|PageModelInterface
     */
    public function find($pageId)
    {
        return $this->model->currentVersion()->find($pageId);
    }

    public function findByInternalName($name)
    {
        return $this->model->where(Model::ATTR_INTERNAL_NAME, '=', $name)->first();
    }

    public function findByParentId($parentId)
    {
        $finder = new Finder\Finder();
        $finder->addFilter(new Finder\ParentId($parentId));

        return $finder->findAll();
    }

    /**
     * @param array|string $uri
     *
     * @return null|Model|Collection
     */
    public function findByPrimaryUri($uri)
    {
        $site = Router::getActiveSite();

        return $this->findBySiteAndPrimaryUri($site, $uri);
    }

    /**
     * @param SiteInterface $site
     * @param array|string  $uri
     *
     * @return null|Model|Collection
     */
    public function findBySiteAndPrimaryUri(SiteInterface $site, $uri)
    {
        $query = $this->model->where(Model::ATTR_SITE, '=', $site->getId());

        if (is_array($uri)) {
            return $query->where(Model::ATTR_PRIMARY_URI, 'in', $uri)->get();
        }

        return $query->where(Model::ATTR_PRIMARY_URI, '=', $uri)->first();
    }

    /**
     * Find a page by URI.
     *
     * @param array|string $uri
     *
     * @return Moel|Collection
     */
    public function findByUri($uri)
    {
        $site = Router::getActiveSite();

        return $this->findBySiteAndUri($site, $uri);
    }

    /**
     * Find a page by site and URI.
     *
     * @param SiteInterface $site
     * @param array|string  $uri
     *
     * @return null|Model|Collection
     */
    public function findBySiteAndUri(SiteInterface $site, $uri)
    {
        $query = $this->model
            ->join('page_urls', 'page_urls.page_id', '=', 'pages.id')
            ->select('pages.*')
            ->where('pages.'.Model::ATTR_SITE, '=', $site->getId());

        if (is_array($uri)) {
            return $query->where('location', 'in', $uri)->get();
        }

        return $query->where('location', '=', $uri)->first();
    }

    /**
     * Returns whether a given page internal name is already in use.
     *
     * @param string $name
     *
     * @return bool
     */
    public function internalNameExists($name)
    {
        return $this->model
            ->where(Model::ATTR_INTERNAL_NAME, $name)
            ->exists();
    }

    /**
     * Save a page.
     *
     * @param PageModelInterface $page
     *
     * @return PageModelInterface
     */
    public function save(PageModelInterface $page)
    {
        $page->save();

        return $page;
    }
}
