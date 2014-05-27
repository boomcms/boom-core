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

	public function find()
	{
		$model = parent::find();
		return new \Boom\Page($model);
	}

	public function findAll()
	{
		$pages = parent::findAll();

		return new Page\Result($pages);
	}
}