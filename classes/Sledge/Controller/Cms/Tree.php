<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Tree controller.
* Generate page trees for cms, site, or feature box.
*
* @package Sledge
* @category Controllers
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Sledge_Controller_Cms_Tree extends Kohana_Controller
{
	private $_query;

	public function before()
	{
		$this->_query = DB::select( array('pages.id', 'page_id'), 'v.children_ordering_policy', 'pages.visible', 'v.visible_in_nav', 'page_links.location', 'v.title', 'page_mptt.*')
					->from('pages')
					->join('page_mptt')
					->on('page_mptt.id', '=', 'pages.id')
					->join('page_links', 'inner')
					->on('page_links.page_id', '=', 'pages.id')
					->where('is_primary', '=', TRUE)
					->where('v.deleted', '=', FALSE);
	}

	/**
	* Generate a full page tree, for use with feature boxes etc.
	*/
	public function action_full()
	{
		$this->_query->join( array('page_versions', 'v'), 'inner')
			  ->on('pages.active_vid', '=', 'v.id');
	}

	public function after()
	{
		if ( ! $this->response->body())
		{
			$this->_query->order_by('page_mptt.lft', 'asc');
			$pages = $this->_query->execute()->as_array();

			$v = View::factory('site/nav/tree');
			$v->pages = $pages;
			$v->page = ORM::factory('Page', $this->request->param('id'));

			$v->state = ($this->request->param('id') == 'expanded')? 'expanded' : 'collapsed';

			$this->response->body($v);
		}
	}
}