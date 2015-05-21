<?php

namespace BoomCMS\Core\Finder;

use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    abstract public function execute(Builder $query);

    public function shouldBeApplied()
    {
        return true;
    }
}
