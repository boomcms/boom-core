<?php

class Boom_Page_Query
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

	public function __construct(ORM $query, Editor $editor = NULL)
	{
		$this->_editor = $editor === NULL? Editor::instance() : $editor;
		$this->_query = $query;
	}

	public function execute($exclude_deleted = true)
	{
		$this->_query
			->join(array($this->_get_current_version_subquery(), 'v2'), 'inner')
			->on('page.id', '=', 'v2.page_id')
			->join(array('page_versions', 'version'), 'inner')
			->on('page.id', '=', 'version.page_id')
			->on('v2.id', '=', 'version.id');

		if ($exclude_deleted)
		{
			$this->_query->where('version.page_deleted', '=', false);
		}

		// Logged out view?
		if ($this->_editor->state_is(Editor::DISABLED))
		{
			// Get the most recent published version for each page.
			$this->_query
				->where('visible', '=', true)
				->where('visible_from', '<=', $this->_editor->live_time())
				->and_where_open()
					->where('visible_to', '>=', $this->_editor->live_time())
					->or_where('visible_to', '=', 0)
				->and_where_close();
		}

		return $this->_query;
	}

	protected function _get_current_version_subquery()
	{
		$query = DB::select(array(DB::expr('max(id)'), 'id'), 'page_id')
			->from('page_versions')
			->where('stashed', '=', 0)
			->group_by('page_id');

		if ($this->_editor->state_is(Editor::DISABLED))
		{
			$query
				->where('embargoed_until', '<=', DB::expr(time()))
				->where('published', '=', DB::expr(1));
		}

		return $query;
	}

	public static function join_version(ORM $query, $editor_state = NULL)
	{
		$page_query = new static($query, $editor_state);
		return $page_query->execute();
	}
}