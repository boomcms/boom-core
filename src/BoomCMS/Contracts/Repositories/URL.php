<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Contracts\Models\URL as URLInterface;

interface URL
{
    /**
     * @param string $location
     * @param PageInterface    $page
     * @param bool   $isPrimary
     *
     * @return URLInterface
     */
    public function create($location, PageInterface $page, $isPrimary = false);

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
     * @param SiteInterface $site
     * @param string $location
     *
     * @return URLInterface
     */
    public function findBySiteAndLocation(SiteInterface $site, $location);

    /**
     * @param URLInterface $url
     *
     * @return URLInterface
     */
    public function save(URLInterface $url);
}
