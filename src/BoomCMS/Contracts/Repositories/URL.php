<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\URL as URLInterface;

interface URL
{
   /**
     * @param string $location
     * @param int $pageId
     * @param bool $isPrimary
     *
     * @return URLInterface
     */
    public function create($location, $pageId, $isPrimary = false);

    /**
     * @param URLInterface $url
     *
     * @return $this
     */
    public function delete(URLInterface $url);

    /**
     * @param int $id
     *
     * @return URLInterface
     */
    public function find($id);

    /**
     * @param string $location
     *
     * @return URLInterface
     */
    public function findByLocation($location);

    /**
     * @param URLInterface $url
     *
     * @return URLInterface
     */
    public function save(URLInterface $url);
}
