<?php

namespace Boom\Page;

use Boom\Editor as Editor;

class Query
{
	/**
	 *
	 * @var Editor
	 */
	protected $_editor;

	/**
	 *
	 * @var ORM
	 */
	protected $_query;

	public function __construct(\ORM $query, Editor $editor = null)
	{
		$this->_editor = $editor === null? Editor::instance() : $editor;
		$this->_query = $query;
	}

	public function execute()
	{
		$this->_query
			->join(array($this->_getCurrentVersionSubquery(), 'v2'), 'inner')
			->on('page.id', '=', 'v2.page_id')
			->join(array('page_versions', 'version'), 'inner')
			->on('page.id', '=', 'version.page_id')
			->on('v2.id', '=', 'version.id');

		// Logged out view?
		if ($this->_editor->isDisabled())
		{
			// Get the most recent published version for each page.
			$this->_query
				->where('visible', '=', true)
				->where('visible_from', '<=', $this->_editor->getLiveTime())
				->and_where_open()
					->where('visible_to', '>=', $this->_editor->getLiveTime())
					->or_where('visible_to', '=', 0)
				->and_where_close();
		}

		return $this->_query;
	}

	protected function _getCurrentVersionSubquery()
	{
		$query = \DB::select(array(\DB::expr('max(id)'), 'id'), 'page_id')
			->from('page_versions')
			->where('stashed', '=', 0)
			->group_by('page_id');

		if ($this->_editor->isDisabled())
		{
			$query
				->where('embargoed_until', '<=', \DB::expr(time()))
				->where('published', '=', \DB::expr(1));
		}

		return $query;
	}

	public static function joinVersion(ORM $query, $editor_state = null)
	{
		$page_query = new static($query, $editor_state);
		return $page_query->execute();
	}
}