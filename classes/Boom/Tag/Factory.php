<?php

namespace Boom\Tag;

use \Model_Tag as Model_Tag;

abstract class Factory
{
	public static function byName($name)
	{
		return new Tag(new Model_Tag(array('name' => $name)));
	}
}