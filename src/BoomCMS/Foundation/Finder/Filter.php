<?php

namespace BoomCMS\Foundation\Finder;

use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    public function build(Builder $query)
    {
        return $query;
    }

    public function execute(Builder $query)
    {
        return $query;
    }

    public function shouldBeApplied()
    {
        return true;
    }

    public function shouldNotBeApplied()
    {
        return false;
    }
}
