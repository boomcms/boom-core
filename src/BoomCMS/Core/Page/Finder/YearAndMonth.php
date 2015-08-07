<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Finder\Filter;
use DB;
use Illuminate\Database\Eloquent\Builder;

class YearAndMonth extends Filter
{
    const EPOC_FIRST_YEAR = 1970;

    protected $_year;
    protected $_month;

    public function __construct($year, $month)
    {
        $this->_year = $year;
        $this->_month = $month;
    }

    public function build(Builder $query)
    {
        return $query
            ->where(DB::raw('year(from_unixtime(visible_from))'), '=', $this->_year)
            ->where(DB::raw('month(from_unixtime(visible_from))'), '=', $this->_month);
    }

    public function monthIsValid()
    {
        return ctype_digit($this->_month) && $this->_month > 0 && $this->_month <= 12;
    }

    public function shouldBeApplied()
    {
        return $this->yearIsValid() && $this->monthIsValid();
    }

    public function yearIsValid()
    {
        return ctype_digit($this->_year) && $this->_year >= static::EPOC_FIRST_YEAR && $this->_year <= date('Y');
    }
}
