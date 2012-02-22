<?php defined('SYSPATH') or die('No direct script access.');

/**
* Tree controller.
* Generate page trees for cms, site, or feature box.
*
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Tree extends Kohana_Controller
{
	private $_query;
	
	public function before()
	{
		$mptt = ORM::factory( 'page_mptt' )->where( 'page_id', '=', $this->request->param( 'id' ) )->find();
		
		$this->_query = DB::SELECT( array( 'page.id', 'page_id' ), 'page_uri.uri', 'v.title', 'page_mptt.*' )
					->from( 'page' )
					->join( 'page_mptt' )
					->on( 'page_mptt.page_id', '=', 'page.id' )
					->join( 'page_uri', 'inner' )
					->on( 'page_uri.page_id', '=', 'page.id' )
					->where( 'scope', '=', $mptt->scope )
					->where( 'lvl', '!=', 1 )
					->where( 'primary_uri', '=', true )
					->where( 'v.deleted', '=', false );
	}
	
	public function action_leftnav()
	{
		if (Auth::instance()->logged_in())
		{
			$this->_query->join( array( 'page_v', 'v'), 'inner' )
				  ->on( 'page.active_vid', '=', 'v.id' )
				  ->where( 'v.visible_in_leftnav_cms', '=', true );
		}
		else
		{
			$this->_query->join( array( 'page_v', 'v'), 'inner' )
				  ->on( 'page.published_vid', '=', 'v.id' )
				  ->where( 'v.visible_from', '<=', time() )
				  ->and_where_open()
				  ->or_where_open()
				  ->where( 'v.visible_to', '>=', time() )
				  ->or_where( 'v.visible_to', '=', 0 )
				  ->or_where_close()
				  ->and_where_close()
				  ->where( 'v.visible_in_leftnav', '=', true )
				  ->where( 'page.published_vid', '!=', null )
				  ->where( 'page.visible', '=', true );
		}
	}
	
	public function after()
	{
		$this->_query->order_by( 'page_mptt.lft', 'asc' );
		$pages = $this->_query->execute()->as_array();
						
		$v = View::factory( 'site/nav/tree' );
		$v->pages = $pages;
		$v->page = ORM::factory( 'page', $this->request->param( 'id' ) );
		
		$this->response->body( $v );
	}
}

?>