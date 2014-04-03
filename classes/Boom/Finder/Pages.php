<?php defined('SYSPATH') OR die('No direct script access.');

class Boom_Finder_Pages extends Finder
{
	protected $_parent_id;

	protected $_sort_applied = FALSE;

	public function __construct()
	{
		$this->_query = ORM::factory('Page')
			->with_current_version(Editor::instance())
			->where('page.primary_uri', '!=', NULL);
	}

	protected function _apply_tag_filter(Model_Tag $tag)
	{
		$this->_query
			->join('pages_tags', 'inner')
			->on('page.id', '=', 'pages_tags.page_id')
			->where('pages_tags.tag_id', '=', $tag->id);
	}

	protected function _get_navigation_visibility_column()
	{
		return (Editor::instance()->state_is(Editor::EDIT))? 'visible_in_nav_cms' : 'visible_in_nav';
	}

	/**
	 *
	 * TODO: this and which_are_children_of_the_page_by_id() function probably belong in a Finder_Pages_Children decorator.
	 */
	public function apply_default_sort()
	{
		if ($this->_parent_id)
		{
			$parent = new Model_Page($this->_parent_id);
			list($sort_column, $sort_direction) = $parent->get_child_ordering_policy();
			$this->sorted_by_property_and_direction($sort_column, $sort_direction);
		}

		return $this;
	}

	public function by_template(Model_Template $template)
	{
		$this->_query->where('template_id', '=', $template->id);

		return $this;
	}

	public function exclude_pages_invisible_in_navigation()
	{
		$this->_query->where($this->_get_navigation_visibility_column(), '=', TRUE);
		return $this;
	}

	public function get_query()
	{
		return $this->_query;
	}

	public function get_results($limit = NULL, $offset = NULL)
	{
		if ( ! $this->_sort_applied)
		{
			$this->apply_default_sort();
		}

		return parent::get_results($limit, $offset);
	}

	public function sorted_by_title()
	{
		return $this->sorted_by_property_and_direction('version.title', 'asc');
	}

	public function sorted_by_manual_order()
	{
		return $this->sorted_by_property_and_direction('sequence', 'asc');
	}

	public function which_are_children_of_the_page_by_id($page_id)
	{
		$this->_parent_id = $page_id;

		$this->_query
			->join('page_mptt', 'inner')
			->on('page.id', '=', 'page_mptt.id')
			->where('page_mptt.parent_id', '=', $page_id);

		return $this;
	}

	public function with_the_most_recent_first()
	{
		return $this->sorted_by_property_and_direction('visible_from', 'desc');
	}

	public function with_the_most_recently_edited_first()
	{
		return $this->sorted_by_property_and_direction('edited_time', 'desc');
	}
}