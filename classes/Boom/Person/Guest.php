<?php

namespace Boom\Person;

class Guest extends \Boom\Person
{
	public function __construct()
	{
		$this->person = new \Model_Person;
	}
}