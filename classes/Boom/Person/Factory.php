<?php

namespace Boom\Person;

abstract class Factory
{
	public static function byId($id)
	{
		return new \Boom\Person(new \Model_Person($id));
	}

	public static function byEmail($email)
	{
		return new \Boom\Person(new \Model_Person(array('email' => $email)));
	}
}