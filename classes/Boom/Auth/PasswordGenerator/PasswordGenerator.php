<?php

namespace Boom\Auth\PasswordGenerator;

abstract class PasswordGenerator
{
	public static $default = 'GenPhrase';

	public static function factory($driver = null)
	{
		$driver === null && $driver = static::$default;

		$class = "\Boom\Auth\PasswordGenerator\\$driver";
		return new $class;
	}

	abstract public function get_password();
}