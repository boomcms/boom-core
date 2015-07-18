<?php

namespace BoomCMS\Core\Tag\Finder;

use BoomCMS\Core\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class SlugEquals extends Filter
{
    protected $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    public function build(Builder $query)
    {
        return $query->where('slug', $this->string);
    }
}
