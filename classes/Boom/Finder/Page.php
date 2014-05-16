<?php

namespace Boom\Finder;

use Boom\Editor as Editor;

class Page extends \Boom\Finder
{
	const TITLE = 'version.title';
	const MANUAL = 'sequence';
	const DATE = 'visible_from';
	const EDITED = 'edited_time';

	public function __construct(Editor $editor = null)
	{
		$editor = $editor?: Editor::instance();

		$this->_query = \ORM::factory('Page')
			->where('deleted', '=', false)
			->with_current_version($editor)
			->where('page.primary_uri', '!=', null);
	}

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
		$finder = new static;

		return $finder
			->addFilter(\Boom\Finder\Page\Filter\Uri($uri))
			->find();
	}

	public function find()
	{
		$pages = parent::find();

		return new Page\Result($pages);
	}
}