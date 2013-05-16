<?php defined('SYSPATH') OR die('No direct script access.');

class Boom_Finder_Paginated extends Finder
{
	protected $_current_page;
	protected $_finder;
	protected $_results_perpage;
	protected $_pagination_view = 'pagination/hoop';

	public function __construct(Finder $finder)
	{
		$this->_finder = $finder;
	}

	public function set_current_page_number($page)
	{
		$this->_current_page = $page;

		return $this;
	}

	public function set_pagination_view($view_filename)
	{
		$this->_pagination_view = $view_filename;

		return $this;
	}

	public function set_results_per_page($perpage)
	{
		$this->_results_perpage = $perpage;

		return $this;
	}

	public function get_results($limit = NULL)
	{
		$total_matching = $this->_finder->count_matching();
		$this->_apply_pagination();
		$pagination_links = $this->_build_pagination_links($total_matching);

		return array($pagination_links, $this->_finder->get_results($limit));
	}

	protected function _apply_pagination()
	{
		$offset = ($this->_current_page - 1) * $this->_results_perpage;

		$this->_finder->_query
			->offset($offset)
			->limit($this->_results_perpage);
	}

	protected function _build_pagination_links($total)
	{
		return Pagination::factory(array(
				'current_page'	=>	array(
					'key'		=>	'page',
					'source'	=>	'mixed',
				),
				'total_items'		=>	$total,
				'items_per_page'	=>	$this->_results_perpage,
				'view'			=>	$this->_pagination_view,
				'count_in'			=>	1,
				'count_out'		=>	1,
			));
	}

	protected function _apply_tag_filter(\Model_Tag $tag)
	{
		$this->_finder->_apply_tag_filter($tag);

		return $this;
	}

	public function sorted_by_title()
	{
		$this->_finder->sorted_by_title();

		return $this;
	}

	public function with_the_most_recent_first()
	{
		$this->_finder->with_the_most_recent_first();

		return $this;
	}

	public function __call($name, $arguments)
	{
		call_user_func_array(array($this->_finder, $name), $arguments);
		return $this;
	}
}