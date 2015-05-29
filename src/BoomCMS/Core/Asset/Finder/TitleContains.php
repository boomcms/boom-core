<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Core\Finder\Filter as BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class TitleContains extends BaseFilter
{
    protected $title;

    public function __construct($title = null)
    {
        $this->title = trim($title);
    }

    public function execute(Builder $query)
    {
        return $query
            ->and_where_open()
            ->where('title', 'like', "%{$this->title}%")
            ->or_where('description', 'like', "%{$this->title}%")
            ->and_where_close();
    }

    public function shouldBeApplied()
    {
        return $this->title ? true : false;
    }
}
