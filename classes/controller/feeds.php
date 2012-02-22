<?php defined('SYSPATH') or die('No direct script access.');

/**
* Controller for displaying feeds of any kind, but only RSS is supported at the moment.
* This controller doesn't extend any of the Sledge base controllers because it doesn't need any of their templating code.
* It is it's own little island in the Sledge, refusing to mix with the other controllers.
*
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Feeds extends Kohana_Controller
{
	/**
	* Holds the requested page.
	* @access private
	* @var object
	*/
	private $_page;
	
	/**
	* The current template.
	* @access private
	* @var View
	*/
	private $_template;
	
	/**
	* Method called before the main controller method (the clue's in the name).
	* Handles finding the page which is being RSS'd and checking that RSS feeds are enabled.
	*/
	public function before()
	{
		$uri = $this->request->param( 'uri' );
		
		// Find the page.
		$page_uri = ORM::factory( 'page_uri' )->where( 'uri', '=', $uri )->find();
		
		if ($page_uri->loaded())
		{
			if ($page_uri->page->loaded() && $page_uri->page->enable_rss)
			{
				$this->_page = $page_uri->page;
			}
			else
			{
				$this->action_404();
			}	
		}
		else
		{
			$this->action_404();
		}
	}
	
	public function action_rss()
	{
		$children = ORM::factory( 'page' )
					->join( 'page_mptt', 'inner' )
					->on( 'page_mptt.page_id', '=', 'page.id' )
					->where( 'scope', '=', $this->_page->mptt->scope )
					->where( 'lft', '>', $this->_page->mptt->lft )
					->where( 'rgt', '>', $this->_page->mptt->rgt )
					->order_by( 'visible_from', 'desc' )
					->find_all();
					
		$this->_template = View::factory( 'feeds/rss' );
		$this->_template->page = $this->_page;
		$this->_template->children = $children;
	}
	
	public function action_404()
	{
		$this->response->body( Request::factory( 'error/404' )->execute() );
	}
	
	public function after()
	{
		$this->response->headers( 'content-type', 'application/xml' );
		$this->response->body( $this->_template );
	}
}

?>
