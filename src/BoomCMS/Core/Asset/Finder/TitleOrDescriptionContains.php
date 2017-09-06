<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Foundation\Finder\Filter as BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class TitleOrDescriptionContains extends BaseFilter
{
    protected $text;

    public function __construct($text = null)
    {
        $this->text = trim(strip_tags($text));
    }

    public function build(Builder $query)
    {
        return $query
            ->where(function (Builder $query) {
                $query
                    ->where('title', 'like', "%{$this->text}%")
                    ->orWhere('description', 'like', "%{$this->text}%");
            });
    }

    /**
     * @return bool
     */
    public function shouldBeApplied()
    {
        return !empty($this->text);
    }
}
