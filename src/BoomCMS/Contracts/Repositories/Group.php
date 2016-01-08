<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Group as GroupInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Database\Models\Group as GroupModel;

interface Group
{
    public function __construct(GroupModel $model = null);

    public function create(array $attrs);

    public function delete(GroupInterface $group);

    public function find($id);

    public function findAll();

    /**
     * @param SiteInterface $site
     */
    public function findBySite(SiteInterface $site);

    /**
     * @param SiteInterface $site
     * @param array         $groupIds
     */
    public function findBySiteExcluding(SiteInterface $site, array $groupIds);

    public function save(GroupInterface $group);
}
