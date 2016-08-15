<?php

namespace BoomCMS\Page\Finder;

use BoomCMS\Foundation\Finder\Filter;
use BoomCMS\Support\Facades\Editor;
use Illuminate\Database\Eloquent\Builder;

class VisibleInNavigation extends Filter
{
    protected function getNavigationVisibilityColumn()
    {
        return (Editor::isEnabled()) ? 'visible_in_nav_cms' : 'visible_in_nav';
    }

    public function build(Builder $query)
    {
        return $query->where($this->getNavigationVisibilityColumn(), '=', true);
    }
}
