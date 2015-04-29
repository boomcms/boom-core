<?php

namespace BoomCMS\Core\Page\Finder;

use DB;

class Year extends \Boom\Finder\Filter
{
    const EPOC_FIRST_YEAR = 1970;

    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function execute(\ORM $query)
    {
        return $query->where(DB::expr('year(from_unixtime(visible_from))'), '=', $this->year);
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
