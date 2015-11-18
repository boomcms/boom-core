<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Database\Models\Group as GroupModel;
use BoomCMS\Contracts\Models\Group as GroupInterface;

interface Group
{
    public function __construct(GroupModel $model = null);

    public function create(array $attrs);

    public function delete(GroupInterface $group);

    public function find($id);

    public function findAll();

    public function save(GroupInterface $group);
}
