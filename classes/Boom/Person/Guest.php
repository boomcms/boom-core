<?php

namespace Boom\Person;

class Guest extends \Boom\Person
{
	public function __construct()
	{
		$this->model = new \Model_Person;
	}
}