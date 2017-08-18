<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Tag as TagInterface;

interface Tag extends Repository
{
    /**
     * @param SiteInterface $site
     * @param string        $name
     * @param string        $group
     *
     * @return TagInterface
     */
    public function create($name, $group);

    /**
     * @param string $name
     *
     * @return TagInterface
     */
    public function findByName($name);

    /**
     * @param string $name
     * @param string $group
     *
     * @return TagInterface
     */
    public function findByNameAndGroup($name, $group = null);

    /**
     * @param string $slug
     * @param string $group
     *
     * @return TagInterface
     */
    public function findBySlugAndGroup($slug, $group = null);

    /**
     * @param string $name
     * @param string $group
     *
     * @return TagInterface
     */
    public function findOrCreate($name, $group = null);
}
