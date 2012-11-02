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
		$this->_query = DB::select( array('pages.id', 'page_id'), 'v.child_ordering_policy', 'page.visible', 'v.visible_in_leftnav', 'page_uris.uri', 'v.title', 'page_mptt.*')
					->from('pages')
					->join('page_mptt')
					->on('page_mptt.id', '=', 'pages.id')
					->join('page_uris', 'inner')
					->on('page_uris.page_id', '=', 'pages.id')
					->where('primary_uri', '=', TRUE)
					->where('v.deleted', '=', FALSE);
	}
	
	/**
	* Generate a page tree for leftnavs.
	*/
	public function action_leftnav()
	{
		$min_depth = ($this->request->post('min_depth'))? $this->request->post('min_depth') : 1;

		$mptt = ORM::factory('Page_mptt')->where('id', '=', $this->request->param('id'))->find();
		
		// Get the top level page. We look for all children of this page.
		$top = ORM::factory('Page_mptt')
					->where('lvl', '=', $min_depth)
					->where('scope', '=', $mptt->scope)
					->where('lft', '<=', $mptt->lft)
					->where('rgt', '>=', $mptt->rgt)
					->find();

		$this->_query->where('lft', '>', $top->lft)
					->where('rgt', '<', $top->rgt)
					->where('scope', '=', $top->scope);
		
		if ($this->auth->logged_in() AND Editor::state() === Editor::EDIT)
		{
			$this->_query->join( array('page_versions', 'v'), 'inner')
				  ->on('pages.active_vid', '=', 'v.id')
				  ->where('v.visible_in_leftnav_cms', '=', TRUE);
		}
		else
		{
			$this->_query->join( array('page_versions', 'v'), 'inner')
				  ->on('pages.published_vid', '=', 'v.id')
				  ->where('v.visible_from', '<=', time())
				  ->and_where_open()
				  ->or_where_open()
				  ->where('v.visible_to', '>=', time())
				  ->or_where('v.visible_to', '=', 0)
				  ->or_where_close()
				  ->and_where_close()
				  ->where('v.visible_in_leftnav', '=', TRUE)
				  ->where('pages.published_vid', '!=', NULL)
				  ->where('page.visible', '=', TRUE);
		}
	}
	
	/**
	* Navigation tree for sites like NHHG.
	*/
	public function action_mainnav()
	{
		$mptt = ORM::factory('Page_mptt')->where('id', '=', $this->request->param('id'))->find();
		$this->_query->where('scope', '=', $mptt->scope)
				->where('lvl', '=', 2);
		
		if ($this->auth->logged_in())
		{
			$this->_query->join( array('page_versions', 'v'), 'inner')
				  ->on('pages.active_vid', '=', 'v.id')
				  ->where('v.visible_in_leftnav_cms', '=', TRUE);
		}
		else
		{
			$this->_query->join( array('page_versions', 'v'), 'inner')
				  ->on('pages.published_vid', '=', 'v.id')
				  ->where('v.visible_from', '<=', time())
				  ->and_where_open()
				  ->or_where_open()
				  ->where('v.visible_to', '>=', time())
				  ->or_where('v.visible_to', '=', 0)
				  ->or_where_close()
				  ->and_where_close()
				  ->where('v.visible_in_leftnav', '=', TRUE)
				  ->where('pages.published_vid', '!=', NULL)
				  ->where('page.visible', '=', TRUE);
		}
		
		$this->_query->order_by('page_mptt.lft', 'asc');
		$pages = $this->_query->execute()->as_array();
						
		$v = View::factory('site/nav/main');
		$v->pages = $pages;
		$v->count = count($pages);
		
		$this->response->body($v);
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