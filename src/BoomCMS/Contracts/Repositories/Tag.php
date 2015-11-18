<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Tag as TagInterface;

interface Tag
{
    /**
     * @param string $name
     * @param string $group
     *
     * @return TagInterface
     */
    public function create($name, $group);

    /**
     * @param int $id
     *
     * @return TagInterface
     */
    public function find($id);

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
    public function findBySlugAndSlug($slug, $group = null);

    /**
     * @param string $name
     * @param string $group
     *
     * @return TagInterface
     */
    public function findOrCreateByNameAndGroup($name, $group = null);
}
