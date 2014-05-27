<?php

namespace Boom\Page;

abstract class Factory
{
	public static function byId($id)
	{
		return new \Boom\Page(new \Model_Page(array('id' => $id, 'deleted' => false)));
	}

	public static function byInternalName($name)
	{
		return new \Boom\Page(new \Model_Page(array('internal_name' => $name, 'deleted' => false)));
	}

	public static function byPrimaryUri($uri)
	{
		return new \Boom\Page(new \Model_Page(array('primary_uri' => $uri, 'deleted' => false)));
	}

	public static function byUri($uri)
	{
		$finder = new \Boom\Finder\Page;

		return $finder
			->addFilter(\Boom\Finder\Page\Filter\Uri($uri))
			->find();
	}
}