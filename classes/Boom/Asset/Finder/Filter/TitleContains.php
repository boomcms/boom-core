<?php

namespace Boom\Asset\Finder\Filter;

class TitleContains extends \Boom\Finder\Filter
{
	protected $title;

	public function __construct($title = null)
	{
		$this->title = trim($title);
	}

	public function execute(\ORM $query)
	{
		return $query
			->and_where_open()
			->where('title', 'like', "%{$this->title}%")
			->or_where('description', 'like', "%{$this->title}%")
			->and_where_close();
	}

	public function shouldBeApplied()
	{
		return $this->title? true : false;
	}
}