<?php

namespace BoomCMS\Support\Traits;

use BoomCMS\Support\Facades\Helpers;
use Illuminate\Database\Eloquent\Collection;

trait ListsPages
{
    /**
     * Returns a collection of pages based on the given criteria.
     *
     * @param array $params
     *
     * @return Collection
     */
    public function getPages(array $params): Collection
    {
        return Helpers::getPages($params);
    }
}
