<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Database\Models\Site as SiteModel;

interface Site
{
    /**
     * @param SiteModel $model
     */
    public function __construct(SiteModel $model = null);

    /**
     * @param array $attrs
     *
     * @return SiteModel
     */
    public function create(array $attrs);

    /**
     * @param SiteInterface $group
     */
    public function delete(SiteInterface $site);

    /**
     * @param int $id
     *
     * @return SiteInterface
     */
    public function find($id);

    public function findAll();

    /**
     * @param string $hostname
     *
     * @return null|SiteInterface
     */
    public function findByHostname($hostname);

    /**
     * @param SiteInterface $site
     *
     * @return SiteInterface
     */
    public function save(SiteInterface $site);
}
