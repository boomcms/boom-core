<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Contracts\Models\Tag as TagInterface;
use BoomCMS\Contracts\Repositories\Tag as TagRepositoryInterface;
use BoomCMS\Database\Models\Tag as Model;
use InvalidArgumentException;

class Tag implements TagRepositoryInterface
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
     * @param Model $model
     * @param SiteInterface $site
     */
    public function __construct(Model $model, SiteInterface $site)
    {
        $this->model = $model;
        $this->site = $site;
    }

    /**
     * @param string $name
     * @param string $group
     *
     * @throws InvalidArgumentException
     *
     * @return TagInterface
     */
    public function create($name, $group)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Tag name must not be empty');
        }

        return $this->model->create([
            Model::ATTR_NAME  => $name,
            Model::ATTR_GROUP => $group,
        ]);
    }

    /**
     * Returns the tag with the given ID.
     *
     * @param int $tagId
     *
     * @return null|TagInterface
     */
    public function find($tagId)
    {
        return $this->model->find($tagId);
    }

    /**
     * @param string $name
     *
     * @return TagInterface
     */
    public function findByName($name)
    {
        return $this->model
            ->whereSiteIs($this->site)
            ->where(Model::ATTR_NAME, '=', $name)
            ->first();
    }

    /**
     * @param string $name
     * @param string $group
     *
     * @return TagInterface
     */
    public function findByNameAndGroup($name, $group = null)
    {
        return $this->model
            ->whereSiteIs($this->site)
            ->where(Model::ATTR_NAME, '=', $name)
            ->where(Model::ATTR_GROUP, '=', $group)
            ->first();
    }

    /**
     * @param SiteInterface $site
     *
     * @return TagInterface
     */
    public function findBySite(SiteInterface $site)
    {
        return $this->model
            ->select('tags.*')
            ->where('tags.site_id', $site->getId())
            ->appliedToALivePage()
            ->orderBy('group')
            ->orderBy('name')
            ->get();
    }

    /**
     * @param string $slug
     * @param string $group
     *
     * @return TagInterface
     */
    public function findBySlugAndGroup($slug, $group = null)
    {
        return $this->model
            ->whereSiteIs($this->site)
            ->where(Model::ATTR_SLUG, '=', $slug)
            ->where(Model::ATTR_GROUP, '=', $group)
            ->first();
    }

    /**
     * @param string $name
     * @param string $group
     *
     * @return TagInterface
     */
    public function findOrCreate($name, $group = null)
    {
        // Ensure group is null if an empty string is passed.
        $group = $group ?: null;
        $tag = $this->findByNameAndGroup($name, $group);

        return $tag ?: $this->create($name, $group);
    }
}
