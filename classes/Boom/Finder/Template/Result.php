<?php

namespace Boom\Finder\Template;

class Result extends \ArrayIterator
{
	public function __construct(\Database_Result $results)
	{
		$results = $results->as_array();

		foreach ($results as &$result) {
			$result = new \Boom\Template($result);
		}

		parent::__construct($results);
	}
}