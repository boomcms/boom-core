<?php defined('SYSPATH') OR die('No direct script access.');

class Boom_List_Page_Children_Paginated extends List_Page_Children
{
	protected $_current_page;
	protected $_results_perpage;
	protected $_pagination_view = 'pagination/hoop';

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
		$total_matching = $this->count_matching();
		$this->_apply_pagination();
		$pagination_links = $this->_build_pagination_links($total_matching);

		return array($pagination_links, parent::get_results($limit));
	}

	protected function _apply_pagination()
	{
		$offset = ($this->_current_page - 1) * $this->_results_perpage;

		$this->_query
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
}