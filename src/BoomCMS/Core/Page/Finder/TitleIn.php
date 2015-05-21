<?php

namespace BoomCMS\Core\Page\Finder;
use BoomCMS\Core\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class TitleIn extends Filter
{
    /**
	 *
	 * @var array
	 */
    protected $titles;

    public function __construct(array $titles)
    {
        $this->titles = $titles;
    }

    public function execute(Builder $query)
    {
        return $query->where('title', 'in', $this->titles);
    }

    public function shouldBeApplied()
    {
        return ! empty($this->titles);
    }
}
