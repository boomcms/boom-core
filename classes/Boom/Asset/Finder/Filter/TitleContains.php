<?php

namespace Boom\Asset\Finder\Filter;

class TitleContains extends \Boom\Finder\Filter
{
	protected $_title;

	public function __construct($title = null)
	{
		$this->_title = trim($title);
	}

	public function execute(\ORM $query)
	{
		return $query
			->and_where_open()
			->where('title', 'like', "%{$this->_text}%")
			->or_where('description', 'like', "%{$this->_text}%")
			->and_where_close();
	}

	public function shouldBeApplied()
	{
		return $this->_title? true : false;
	}
}