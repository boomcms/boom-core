<?php

namespace BoomCMS\Core\Asset\Type;

use BoomCMS\Core\Asset\Asset;

class Invalid extends Asset
{
    protected $attributes = [];

    public function __construct()
    {
    }

    public function exists()
    {
        return false;
    }

    public function getType()
    {
        return null;
    }

    public function loaded()
    {
        return false;
    }
}
