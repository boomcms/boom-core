<?php

namespace Boom\Tag\Finder\Filter;

class Type extends \Boom\Finder\Filter
{
    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function execute(\ORM $query)
    {

    }
}
