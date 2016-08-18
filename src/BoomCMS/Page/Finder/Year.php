<?php

namespace BoomCMS\Page\Finder;

use BoomCMS\Foundation\Finder\Filter;
use DB;
use Illuminate\Database\Eloquent\Builder;

class Year extends Filter
{
    const EPOC_FIRST_YEAR = 1970;

    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function build(Builder $query)
    {
        return $query->where(DB::raw('year(from_unixtime(visible_from))'), '=', $this->year);
    }

    public function shouldBeApplied()
    {
        return $this->yearIsValid();
    }

    public function yearIsValid()
    {
        return ctype_digit($this->year) && $this->year >= static::EPOC_FIRST_YEAR && $this->year <= date('Y');
    }
}
