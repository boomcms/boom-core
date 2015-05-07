<?php

namespace BoomCMS\Core\Asset\Type;

use \Boom\Asset\Asset;

class Invalid extends Asset
{
    protected $_model;

    public function __construct()
    {
        $this->model = new \Model_Asset();
    }

    public function exists()
    {
        return false;
    }

    public function getType()
    {
        return NULL;
    }

    public function loaded()
    {
        return false;
    }
}
