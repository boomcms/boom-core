<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class ParentPage extends Filter
{
    protected $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function build(Builder $query)
    {
        $order = $this->parent->getChildOrderingPolicy();

        return $query
            ->where('parent_id', '=', $this->parent->getId())
            ->orderBy($order->getColumn(), $order->getDirection());
    }
}
