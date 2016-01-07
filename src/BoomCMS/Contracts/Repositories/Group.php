<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Group as GroupInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Database\Models\Group as GroupModel;

interface Group
{
    public function __construct(GroupModel $model = null);

    /**
     * @param SiteInterface $site
     * @param string        $name
     *
     * @return GroupModel
     */
    public function create(SiteInterface $site, $name);

    /**
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function delete(GroupInterface $group);

    public function find($id);

    public function findAll();

    /**
     * @param SiteInterface $site
     */
    public function findBySite(SiteInterface $site);

    public function save(GroupInterface $group);
}
