<?php

namespace Boom\Asset;

use \Model_Asset;

class Factory
{
	public static function createFromType($type)
	{
		$model = new Model_Asset;
		$model->type = $type;

		return static::fromModel($model);
	}

	public static function fromModel(Model_Asset $asset)
	{
		$type = Type::numericTypeToClass($asset->type)?: 'Invalid';
		$classname = "\Boom\Asset\\Type\\" . $type;

		return new $classname($asset);
	}
}