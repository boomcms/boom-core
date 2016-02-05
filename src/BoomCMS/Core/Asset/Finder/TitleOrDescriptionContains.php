<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Foundation\Finder\Filter as BaseFilter;
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

    /**
     * @return bool
     */
    public function shouldBeApplied()
    {
        return !empty($this->title);
    }
}
