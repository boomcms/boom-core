<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Database\Models\Group as GroupModel;

interface Group extends Repository
{
    public function __construct(GroupModel $model = null);

    /**
     * @param SiteInterface $site
     * @param string        $name
     *
     * @return GroupModel
     */
    public function create($name);

    public function findAll();

    /**
     * @param SiteInterface $site
     */
    public function findBySite(SiteInterface $site);
}
