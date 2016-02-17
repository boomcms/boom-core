<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Foundation\Finder\Filter;
use DB;
use Illuminate\Database\Eloquent\Builder;

class YearAndMonth extends Filter
{
    const EPOC_FIRST_YEAR = 1970;

    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function build(Builder $query)
    {
        return $query
            ->where(DB::raw('year(from_unixtime(visible_from))'), '=', $this->year)
            ->where(DB::raw('month(from_unixtime(visible_from))'), '=', $this->month);
    }

    public function monthIsValid()
    {
        return ctype_digit($this->month) && $this->month > 0 && $this->month <= 12;
    }

    public function shouldBeApplied()
    {
        return $this->yearIsValid() && $this->monthIsValid();
    }

    public function yearIsValid()
    {
        return ctype_digit($this->year) && $this->year >= static::EPOC_FIRST_YEAR && $this->year <= date('Y');
    }
}
