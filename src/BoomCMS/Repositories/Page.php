<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Page as PageModelInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Contracts\Repositories\Page as PageRepositoryInterface;
use BoomCMS\Core\Page\Finder;
use BoomCMS\Database\Models\Page as Model;
use BoomCMS\Support\Facades\Router;

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

    public function find($id)
    {
        return $this->model->currentVersion()->find($id);
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
     * @param string $uri
     *
     * @return null|Model
     */
    public function findByPrimaryUri($uri)
    {
        $site = Router::getActiveSite();

        return $this->findBySiteAndPrimaryUri($site, $uri);
    }

    /**
     * @param SiteInterface $site
     * @param string        $uri
     *
     * @return null|Model
     */
    public function findBySiteAndPrimaryUri(SiteInterface $site, $uri)
    {
        return $this->model
            ->where(Model::ATTR_SITE, '=', $site->getId())
            ->where(Model::ATTR_PRIMARY_URI, '=', $uri)
            ->first();
    }

    /**
     * Find a page by URI.
     *
     * @param string $uri
     *
     * @return Page
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
     * @param string        $uri
     *
     * @return null|Model
     */
    public function findBySiteAndUri(SiteInterface $site, $uri)
    {
        return $this->model
            ->join('page_urls', 'page_urls.page_id', '=', 'pages.id')
            ->where('location', '=', $uri)
            ->where('pages.'.Model::ATTR_SITE, '=', $site->getId())
            ->select('pages.*')
            ->first();
    }

    /**
     * Returns whether a given page internal name is already in use
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
