<?php

namespace Boom\Page\Finder\Filter;

class VisibleToSearchEngines extends \Boom\Finder\Filter
{
    public function execute(\ORM $query)
    {
        return $query->where('external_indexing', '=', true);
    }
}
