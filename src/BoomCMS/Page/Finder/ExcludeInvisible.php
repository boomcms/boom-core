<?php

namespace BoomCMS\Page\Finder;

use BoomCMS\Foundation\Finder\Filter;
use BoomCMS\Support\Facades\Editor;
use Illuminate\Database\Eloquent\Builder;

class ExcludeInvisible extends Filter
{
    protected $apply;

    public function __construct($apply = null)
    {
        $this->apply = $apply;
    }

    public function build(Builder $query)
    {
        return $query->isVisibleAtTime(time());
    }

    public function shouldBeApplied()
    {
        return $this->apply === true || ($this->apply === null && !Editor::isEnabled());
    }
}
