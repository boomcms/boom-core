<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Core\Finder\Filter as BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class TitleOrDescriptionContains extends BaseFilter
{
    protected $title;

    public function __construct($title = null)
    {
        $this->title = trim($title);
    }

    public function build(Builder $query)
    {
        return $query->where('title', 'like', "%{$this->title}%");
    }

    public function shouldBeApplied()
    {
        return $this->title == null ? false : true;
    }
}
