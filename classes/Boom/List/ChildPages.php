<?php

class Boom_List_ChildPages extends List_ChildPages
{
	protected $_query;

	public function __construct($parent_id)
	{
		$this->_query = $this->_prepare_query($parent_id);
	}

	protected function _navigation_visibility_column()
	{
		return (Editor::instance()->state_is(Editor::EDIT))? 'visible_in_nav_cms' : 'visible_in_nav';
	}

	protected function _prepare_query($parent_id)
	{
		return ORM::factory('Page')
			->join('page_mptt', 'inner')
			->on('page_mptt.id', '=', 'page.id')
			->with_current_version($this->editor)
			->where('page_mptt.parent_id', '=', $parent_id)
			->where('page.primary_uri', '!=', NULL);
	}

	protected function _tag_filter_should_be_applied(Model_Tag $tag)
	{
		return $tag->loaded();
	}

	public function apply_pagination($perpage, $current_page)
	{
		$this->_query
			->offset(($current_page - 1) * $perpage)
			->limit($perpage);
	}

	public function count_matching()
	{
		$count_query = clone $this->_query;
		return $count_query->count_all();
	}

	public static function of_page_by_id($page_id)
	{
		return new static($page_id);
	}

	public function exclude_pages_invisible_in_navigation()
	{
		return $query->where($this->_navigation_visibility_column(), '=', TRUE);
	}

	public function filtered_by_month($month)
	{
		return $this->_query->where(DB::expr('month(from_unixtime(visible_from))'), '=', $month);
	}

	public function filtered_by_year($year)
	{
		return $this->_query->where(DB::expr('year(from_unixtime(visible_from))'), '=', $year);
	}

	public function get_paginated_results($perpage, $current_page)
	{
		$total_matching = $this->count_matching();
		$this->apply_pagination($perpage, $current_page);

		return array($total_matching, $this->get_results());
	}

	public function get_results()
	{
		return $this->_query->find_all();
	}

	public function sorted_by_property($property)
	{

	}

	public function sorted_by_property_and_direction($property, $direction)
	{

	}

	public function which_have_the_tag_named($tag_name)
	{
		$tag = new Model_Tag(array('name' => $tag_name));

		if ($this->_tag_filter_should_be_applied($tag))
		{
			$this->_query
				->join('pages_tags', 'inner')
				->on('page.id', '=', 'pages_tags.page_id')
				->where('pages_tags.tag_id', '=', $tag->id);
		}

		return $this;
	}
}