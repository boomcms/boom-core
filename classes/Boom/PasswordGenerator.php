<?php

abstract class Boom_PasswordGenerator
{
	public static $default = 'GenPhrase';

	public static function factory($driver = null)
	{
		$driver === null AND $driver = static::$default;

		$class = "PasswordGenerator_$driver";
		return new $class;
	}

	abstract public function get_password();
}