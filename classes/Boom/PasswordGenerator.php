<?php

namespace Boom;

abstract class PasswordGenerator
{
	public static $default = 'GenPhrase';

	public static function factory($driver = null)
	{
		$driver === null && $driver = static::$default;

		$class = "Boom\\PasswordGenerator\\$driver";
		return new $class;
	}

	abstract public function get_password();
}