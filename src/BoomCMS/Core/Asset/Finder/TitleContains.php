<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Foundation\Finder\Filter as BaseFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class TitleContains extends BaseFilter
{
    protected $title;

    public function __construct($title = null)
    {
        $this->title = trim($title);
    }

    public function build(Builder $query)
    {
        $text = $this->title;

        return $query
            ->whereNested(function (QueryBuilder $query) use ($text) {
                return $query
                    ->where('title', 'like', "%$text%")
                    ->orWhere('description', 'like', "%$text%");
            });
    }

    /**
     * @return bool
     */
    public function shouldBeApplied()
    {
        return !empty($this->title);
    }
}
