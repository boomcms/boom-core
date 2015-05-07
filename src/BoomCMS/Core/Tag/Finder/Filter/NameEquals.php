<?php

namespace BoomCMS\Core\Tag\Finder\Filter;

class NameEquals extends \Boom\Finder\Filter
{
    protected $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    public function execute(\ORM $query)
    {
        return $query->where('name', '=', $this->string);
    }
}
