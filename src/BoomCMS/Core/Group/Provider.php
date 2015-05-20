<?php

namespace BoomCMS\Core\Group;

use BoomCMS\Core\Models\Group as Model;

use Illuminate\Support\Facades\DB;

class Provider
{
    public function create(array $attributes)
    {

    }

    public function findAll()
    {
        $groups = [];

        foreach (Model::all() as $m) {
            $groups[] = new Group($m->toArray());
        }

        return $groups;
    }

    public function findById($id)
    {
        return new Group(new \Model_Group($id));
    }

    public function findByName($name)
    {

    }
}
