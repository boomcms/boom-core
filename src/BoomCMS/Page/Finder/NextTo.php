<?php

namespace BoomCMS\Page\Finder;

use BoomCMS\Contracts\Models\Page;
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
        list($column, $direction) = $this->currentPage->getParent()->getChildOrderingPolicy();

        if (
            ($this->direction === 'before' && $direction === 'asc') ||
            ($this->direction === 'after' && $direction === 'desc')
        ) {
            $operator = '<';
            $direction = 'desc';
        } else {
            $operator = '>';
            $direction = 'asc';
        }

        return $query
            ->where($column, $operator.'=', $this->getValue($column))
            ->where('pages.id', $operator, $this->currentPage->getId())
            ->limit(1)
            ->orderBy($column, $direction);
    }

    protected function getValue($column)
    {
        switch ($column) {
            case 'visible_from':
                return $this->currentPage->getVisibleFrom()->getTimestamp();
            case 'sequence':
                return $this->currentPage->getManualOrderPosition();
            default:
                return $this->currentPage->getTitle();
        }
    }
}
