<?php

namespace Boom\Finder\Asset\Filter;

class TitleOrDescriptionContains extends \Boom\Finder\Filter
{
	protected $_text;

	public function __construct($text = null)
	{
		$this->_text = trim($text);
	}

	public function execute(\ORM $query)
	{
		$query->where('title', 'like', "%{$this->_title}%");
	}

	public function shouldBeApplied()
	{
		return $this->_text? true : false;
	}
}