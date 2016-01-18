<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Page as PageInterface;

interface Page
{
    /**
     * @param array $attrs
     *
     * @return PageInterface
     */
    public function create(array $attrs);

    /**
     * @param PageInterface $page
     *
     * @return $this
     */
    public function delete(PageInterface $page);

    /**
     * @param mixed $id
     *
     * @return PageInterface
     */
    public function find($id);

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
     * @param string $uri
     *
     * @return PageInterface
     */
    public function findByPrimaryUri($uri);

    /**
     * @param string $uri
     *
     * @return PageInterface
     */
    public function findByUri($uri);

    /**
     * @param PageInterface $page
     *
     * @return PageInterface
     */
    public function save(PageInterface $page);
}
