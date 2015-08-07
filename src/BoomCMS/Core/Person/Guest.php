<?php

namespace BoomCMS\Core\Person;

class Guest extends Person
{
    protected $data = [];

    public function __construct()
    {
    }

    public function isValid()
    {
        return false;
    }

    /**
     * @return \Boom\Person\Guest
     */
    public function save()
    {
        return $this;
    }
}
