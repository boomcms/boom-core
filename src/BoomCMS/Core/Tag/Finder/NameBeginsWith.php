<?php

namespace BoomCMS\Core\Tag\Finder\Filter;

use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class NameBeginsWith extends Filter
{
    protected $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    public function build(Builder $query)
    {
        return $query->where('name', 'like', $this->string.'%');
    }
}
