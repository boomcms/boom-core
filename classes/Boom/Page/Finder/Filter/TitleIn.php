<?php

namespace Boom\Page\Finder\Filter;

class TitleIn extends \Boom\Finder\Filter
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

    public function execute(\ORM $query)
    {
        return $query->where('title', 'in', $this->titles);
    }

    public function shouldBeApplied()
    {
        return ! empty($this->titles);
    }
}
