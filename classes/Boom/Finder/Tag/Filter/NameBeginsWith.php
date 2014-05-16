<?php

namespace Boom\Finder\Tag\Filter;

class NameBeginsWith extends \Boom\Finder\Filter
{
	protected $string;

	public function __construct($string)
	{
		$this->string = $string;
	}

	public function execute(\ORM $query)
	{

	}
}