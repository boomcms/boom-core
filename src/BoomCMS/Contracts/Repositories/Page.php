<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Page as PageInterface;
use Illuminate\Database\Eloquent\Collection;

interface Page extends Repository
{
    /**
     * @param array $attrs
     *
     * @return PageInterface
     */
    public function create(array $attrs);

    /**
     * @param string $name
     *
     * @return PageInterface
     */
    public function findByInternalName($name);

    /**
     * @param int $parentId
     *
     * @return PageInterface
     */
    public function findByParentId($parentId);

    /**
     * @param array|string $uri
     *
     * @return Collection|PageInterface
     */
    public function findByPrimaryUri($uri);

    /**
     * @param array|string $uri
     *
     * @return Collection|PageInterface
     */
    public function findByUri($uri);

    /**
     * Recurse through a section of the page tree and apply a function.
     *
     * @param PageInterface $page
     * @param callable      $closure
     *
     * @return void
     */
    public function recurse(PageInterface $page, callable $closure);
}
