<?php

namespace Boom;

use \Kohana;

abstract class Config
{
	protected static $configGroup = 'boom';

	public static function get($key)
	{
		return Kohana::$config->load(static::$configGroup)->get($key);
	}
}