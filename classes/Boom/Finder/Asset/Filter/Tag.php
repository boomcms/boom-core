<?php

namespace Boom\Finder\Asset\Filter;

class Tag extends \Boom\Finder\Filter
{
	protected $_query;
	protected $_tags;

	public function __construct($tags = null)
	{
		if (is_array($tags)) {
			$tags = array_unique($tags);
		}

		$this->_tags = $tags;
	}

	public function execute(\ORM $query)
	{
		$this->_query = $query;

		if (is_array($this->_tags)) {
			(count($this->_tags) > 1)? $this->filterByMultipleTags($query) : $this->filterBySingleTag($query);
		} else {
			$this->filterBySingleTag($query);
		}

		return $this->_query;
	}

	public function filterByMultipleTags(\ORM $query) {
		$this->_joinTagsTable($query);

		$this->_query
			->join(array('assets_tags', 't2'), 'inner')
			->on("t1.asset_id", '=', "t2.asset_id")
			->where('t2.tag_id', 'IN', $this->_tags)
			->group_by("t1.asset_id")
			->having(DB::expr('count(distinct t2.tag_id)'), '>=', count($this->_tags));
	}

	public function filterBySingleTag(\ORM $query) {
		if ($this->_tags > 0) {
			$this->_joinTagsTable($query);
			$this->_query->where('t1.tag_id', '=', $this->_tags);
		}
	}

	protected function _joinTagsTable(\ORM $query) {
		$this->_query
			->join(array('assets_tags', 't1'), 'inner')
			->on('assets.id', '=', 't1.asset_id')
			->distinct(true);
	}

	public function shouldBeApplied()
	{
		return ! empty($this->_tags);
	}
}