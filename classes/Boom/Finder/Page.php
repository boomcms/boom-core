<?php

namespace Boom\Finder;

class Page
{
	public static function byId($id)
	{
		return new \Boom\Page($id);
	}

	public static function byInternalName($name)
	{
		return new \Boom\Page(array('internal_name' => $name));
	}

	public static function byPrimaryUri($uri)
	{
		return new \Boom\Page(array('primary_uri' => $uri));
	}
}