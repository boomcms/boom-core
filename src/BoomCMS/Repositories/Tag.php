<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Contracts\Models\Tag as TagInterface;
use BoomCMS\Database\Models\Tag as Model;
use BoomCMS\Foundation\Repository;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Tag extends Repository
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

    public function create($name, $group): TagInterface
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Tag name must not be empty');
        }

        return $this->model->create([
            Model::ATTR_NAME  => $name,
            Model::ATTR_GROUP => $group,
        ]);
    }

    public function findByName($name)
    {
        return $this->model
            ->whereSiteIs($this->site)
            ->where(Model::ATTR_NAME, '=', $name)
            ->first();
    }

    public function findByNameAndGroup($name, $group = null)
    {
        return $this->model
            ->whereSiteIs($this->site)
            ->where(Model::ATTR_NAME, '=', $name)
            ->where(Model::ATTR_GROUP, '=', $group)
            ->first();
    }

    public function findBySite(SiteInterface $site): Collection
    {
        return $this->model
            ->select('tags.*')
            ->where('tags.site_id', $site->getId())
            ->appliedToALivePage()
            ->orderBy('group')
            ->orderBy('name')
            ->get();
    }

    public function findBySlugAndGroup($slug, $group = null)
    {
        return $this->model
            ->whereSiteIs($this->site)
            ->where(Model::ATTR_SLUG, '=', $slug)
            ->where(Model::ATTR_GROUP, '=', $group)
            ->first();
    }

    public function findOrCreate($name, $group = null)
    {
        // Ensure group is null if an empty string is passed.
        $group = $group ?: null;
        $tag = $this->findByNameAndGroup($name, $group);

        return $tag ?: $this->create($name, $group);
    }
}
