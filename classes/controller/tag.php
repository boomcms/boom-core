<?php defined('SYSPATH') or die('No direct script access.');

/**
* Tag controller.
* Stuff related to tags.
*
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Tree extends Kohana_Controller
{	
	/**
	* Generate a tag tree.
	*/
	public function action_tree()
	{
		$tags = DB::SELECT( array( 'tag.id', 'tag_id' ), 'version.title', 'tag_mptt.*' )
					->from( 'tag' )
					->join( 'tag_mptt' )
					->on( 'tag_mptt.id', '=', 'tag.id' )
					->where( 'version.deleted', '=', false )
					->order_by( 'ft', 'asc' )
					->find_all()->as_array();

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