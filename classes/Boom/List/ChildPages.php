<?php

class Boom_List_ChildPages
{
	protected $_parent_id;
	protected $_query;

	public function __construct($parent_id)
	{
		$this->_parent_id = $parent_id;
		$this->_query = $this->_prepare_query($parent_id);
	}

	protected function _apply_tag_filter(Model_Tag $tag)
	{
		$this->_query
			->join('pages_tags', 'inner')
			->on('page.id', '=', 'pages_tags.page_id')
			->where('pages_tags.tag_id', '=', $tag->id);
	}

	/**
	 *
	 * TODO: pagination can be moved into a List_ChildPages_Paginated class
	 */
	protected function _build_pagination_links($total, $perpage, $view = NULL)
	{
		return Pagination::factory(array(
				'current_page'	=>	array(
					'key'		=>	'page',
					'source'	=>	'mixed',
				),
				'total_items'		=>	$total,
				'items_per_page'	=>	$perpage,
				'view'			=>	($view)? $view : 'pagination/hoop',
				'count_in'			=>	1,
				'count_out'		=>	1,
			));
	}

	protected function _get_navigation_visibility_column()
	{
		return (Editor::instance()->state_is(Editor::EDIT))? 'visible_in_nav_cms' : 'visible_in_nav';
	}

	protected function _prepare_query($parent_id)
	{
		return ORM::factory('Page')
			->join('page_mptt', 'inner')
			->on('page_mptt.id', '=', 'page.id')
			->with_current_version(Editor::instance())
			->where('page_mptt.parent_id', '=', $parent_id)
			->where('page.primary_uri', '!=', NULL);
	}

	protected function _tag_filter_should_be_applied(Model_Tag $tag)
	{
		return $tag->loaded();
	}

	public function apply_default_sort_and_get_results($limit = NULL)
	{
		$parent = new Model_Page($this->_parent_id);
		list($sort_column, $sort_direction) = $parent->get_child_ordering_policy();
		$this->sorted_by_property_and_direction($sort_column, $sort_direction);

		return $this->get_results($limit);
	}

	public function apply_pagination($perpage, $current_page)
	{
		$this->_query
			->offset(($current_page - 1) * $perpage)
			->limit($perpage);

		return $this;
	}

	public function count_matching()
	{
		$count_query = clone $this->_query;
		return $count_query->count_all();
	}

	/**
	 * @return Boom_List_ChildPages
	 */
	public static function of_page_by_id($page_id)
	{
		return new static($page_id);
	}

	public function exclude_pages_invisible_in_navigation()
	{
		$this->_query->where($this->_get_navigation_visibility_column(), '=', TRUE);
		return $this;
	}

	public function filtered_by_month($month)
	{
		if ($month > 0)
		{
			$this->_query->where(DB::expr('month(from_unixtime(visible_from))'), '=', $month);
		}

		return $this;
	}

	public function filtered_by_year($year)
	{
		if ($year > 0)
		{
			$this->_query->where(DB::expr('year(from_unixtime(visible_from))'), '=', $year);
		}

		return $this;
	}

	public function get_paginated_results($perpage, $current_page, $view = NULL)
	{
		$total_matching = $this->count_matching();
		$this->apply_pagination($perpage, $current_page);
		$pagination_links = $this->_build_pagination_links($total_matching, $perpage, $view);

		return array($pagination_links, $this->get_results());
	}

	public function get_results($limit = NULL)
	{
		if ($limit)
		{
			$this->_query->limit($limit);
		}

		return $this->_query->find_all();
	}

	public function sorted_by_property_and_direction($property, $direction)
	{
		$this->_query->order_by($property, $direction);

		return $this;
	}

	public function sorted_by_title()
	{
		return $this->sorted_by_property_and_direction('title', 'asc');
	}

	public function with_the_most_recent_first()
	{
		return $this->sorted_by_property_and_direction('visible_from', 'desc');
	}

	public function which_have_the_tag_named($tag_name)
	{
		$tag = new Model_Tag(array('name' => $tag_name));

		if ($this->_tag_filter_should_be_applied($tag))
		{
			$this->_apply_tag_filter($tag);
		}

		return $this;
	}
}