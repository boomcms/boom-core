<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Core\Finder\Filter as BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class TitleOrDescriptionContains extends BaseFilter
{
    protected $_text;

    public function __construct($text = null)
    {
        $this->_text = trim($text);
    }

    public function build(Builder $query)
    {
        return $query->where('title', 'like', "%{$this->_title}%");
    }

    public function shouldBeApplied()
    {
        return $this->_text ? true : false;
    }
}
