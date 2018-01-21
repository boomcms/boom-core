<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Page as PageModelInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Database\Models\Page as Model;
use BoomCMS\Foundation\Repository;
use BoomCMS\Page\Finder;

class Page extends Repository
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

    public function create(array $attrs = []): Model
    {
        return Model::create($attrs);
    }

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

    public function findByPrimaryUri($uri)
    {
        $query = $this->model->where(Model::ATTR_SITE, '=', $this->site->getId());

        if (is_array($uri)) {
            return $query->where(Model::ATTR_PRIMARY_URI, 'in', $uri)->get();
        }

        return $query->where(Model::ATTR_PRIMARY_URI, '=', $uri)->first();
    }

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

    public function internalNameExists($name): bool
    {
        return $this->model
            ->withTrashed()
            ->where(Model::ATTR_INTERNAL_NAME, $name)
            ->exists();
    }

    public function recurse(PageModelInterface $page, callable $closure): void
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
