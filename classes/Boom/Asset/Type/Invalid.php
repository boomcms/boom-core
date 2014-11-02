<?php

namespace Boom\Asset\Type;

class Invalid extends \Boom\Asset
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
