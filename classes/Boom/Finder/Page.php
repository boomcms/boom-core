<?php

namespace Boom\Finder;

class Page
{
	public static function byId($id)
	{
		return \ORM::factory('Page')
			->with_current_version(\Boom\Editor::instance())
			->where('page.id', '=', $id)
			->find();
	}

	public static function byInternalName($name)
	{
		return new Page(array('internal_name' => $name));
	}

	public static function byPrimaryUri($uri)
	{
		return new Page(array('primary_uri' => $uri));
	}
}