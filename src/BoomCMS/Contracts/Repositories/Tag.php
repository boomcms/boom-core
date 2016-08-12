<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Contracts\Models\Tag as TagInterface;

interface Tag
{
    /**
     * @param SiteInterface $site
     * @param string        $name
     * @param string        $group
     *
     * @return TagInterface
     */
    public function create(SiteInterface $site, $name, $group);

    /**
     * @param int $tagId
     *
     * @return TagInterface
     */
    public function find($tagId);

    /**
     * @param SiteInterface $site
     * @param string        $name
     *
     * @return TagInterface
     */
    public function findByName(SiteInterface $site, $name);

    /**
     * @param SiteInterface $site
     * @param string        $name
     * @param string        $group
     *
     * @return TagInterface
     */
    public function findByNameAndGroup(SiteInterface $site, $name, $group = null);

    /**
     * @param SiteInterface $site
     * @param string        $slug
     * @param string        $group
     *
     * @return TagInterface
     */
    public function findBySlugAndGroup(SiteInterface $site, $slug, $group = null);

    /**
     * @param SiteInterface $site
     * @param string        $name
     * @param string        $group
     *
     * @return TagInterface
     */
    public function findOrCreate(SiteInterface $site, $name, $group = null);
}
