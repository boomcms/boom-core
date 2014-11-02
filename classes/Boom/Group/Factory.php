<?php

namespace Boom\Group;

abstract class Factory
{
    public static function byId($id)
    {
        return new Group(new \Model_Group($id));
    }
}
