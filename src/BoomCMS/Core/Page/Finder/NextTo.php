<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Page\Page;
use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class NextTo extends Filter
{
    /**
     * @var Page
     */
    protected $currentPage;

    /**
     * @var string
     */
    protected $direction;

    public function __construct(Page $currentPage, $direction)
    {
        $this->currentPage = $currentPage;
        $this->direction = $direction;
    }

    public function build(Builder $query)
    {
        $order = $this->currentPage->getParent()->getChildOrderingPolicy();

        if (
            ($this->direction === 'before' && $order->getDirection() === 'asc') ||
            ($this->direction === 'after' && $order->getDirection() === 'desc')
        ) {
            $operator = '<';
            $direction = 'desc';
        } else {
            $operator = '>';
            $direction = 'asc';
        }

        return $query
            ->where($order->getColumn(), $operator.'=', $this->currentPage->{$order->getAccessor()}())
            ->where('pages.id', $operator, $this->currentPage->getId())
            ->limit(1)
            ->orderBy($order->getColumn(), $direction);
    }
}
