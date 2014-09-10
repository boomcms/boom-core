<?php

namespace Boom\Auth\PasswordGenerator;

abstract class AbstractPasswordGenerator
{
	public static $default = 'GenPhrase';

	public static function factory($driver = null)
	{
		$driver === null && $driver = static::$default;

		$class = "PasswordGenerator\\$driver";
		return new $class;
	}

	abstract public function get_password();
}