<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Page as PageModelInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Contracts\Repositories\Page as PageRepositoryInterface;
use BoomCMS\Database\Models\Page as Model;
use BoomCMS\Foundation\Repository;
use BoomCMS\Page\Finder;
use Illuminate\Database\Eloquent\Collection;

class Page extends Repository implements PageRepositoryInterface
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

    public function create(array $attrs = [])
    {
        return Model::create($attrs);
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
        $query = $this->model->where(Model::ATTR_SITE, '=', $this->site->getId());

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
     * @return null|Model|Collection
     */
    public function findByUri($uri)
    {
        $query = $this->model
            ->join('page_urls', 'page_urls.page_id', '=', 'pages.id')
            ->select('pages.*')
            ->where('pages.'.Model::ATTR_SITE, '=', $this->site->getId());

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
            ->withTrashed()
            ->where(Model::ATTR_INTERNAL_NAME, $name)
            ->exists();
    }

    /**
     * @param PageModelInterface $page
     * @param callable           $closure
     *
     * @return void
     */
    public function recurse(PageModelInterface $page, callable $closure)
    {
        $children = $this->findByParentId($page->getId());

        if (!empty($children)) {
            foreach ($children as $child) {
                $this->recurse($child, $closure);
            }
        }

        $closure($page);
    }
}
