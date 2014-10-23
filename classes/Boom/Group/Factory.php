<?php

namespace Boom\Group;

abstract class Factory
{
	public static function byId($id)
	{
		return new \Boom\Group(new \Model_Group($id));
	}
}