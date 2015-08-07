<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class Title extends Filter
{
    /**
     * @var array
     */
    protected $title;

    public function __construct($title)
    {
        $this->title = $title;
    }

    public function build(Builder $query)
    {
        if (is_array($this->title)) {
            return $query->whereIn('title', $this->title);
        } else {
            return $query->where('title', '=', $this->title);
        }
    }

    public function shouldBeApplied()
    {
        return !empty($this->title);
    }
}
