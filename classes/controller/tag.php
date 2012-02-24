<?php defined('SYSPATH') or die('No direct script access.');

/**
* Tag controller.
* Stuff related to tags.
*
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Tag extends Kohana_Controller
{	
	/**
	* Generate a tag tree.
	*/
	public function action_tree()
	{

		$tags = DB::select( array( 'tag.id', 'tag_id' ), 'version.name', 'tag_mptt.*' )
					->from( 'tag' )
					->join( array( 'tag_v', 'version'), 'inner' )
					->on( 'active_vid', '=', 'version.id' )
					->join( 'tag_mptt' )
					->on( 'tag_mptt.id', '=', 'tag.id' )
					->where( 'version.deleted', '=', false )
					->order_by( 'lft', 'asc' )
					->execute()->as_array();

		$v = View::factory( 'cms/ui/tags/tree' );
		$v->tags = $tags;

		$this->response->body( $v );
	}
}

?>