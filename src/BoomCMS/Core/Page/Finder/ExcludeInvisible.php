<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Foundation\Finder\Filter;
use BoomCMS\Support\Facades\Editor;
use Illuminate\Database\Eloquent\Builder;

class ExcludeInvisible extends Filter
{
    protected $exclude;

    public function __construct($exclude = false)
    {
        $this->exclude = $exclude;
    }

    public function build(Builder $query)
    {
        return $query->isVisibleAtTime(time());
    }

    public function shouldBeApplied()
    {
        return $this->exclude === true && !Editor::isEnabled();
    }
}
