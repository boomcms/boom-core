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
	* Can be passed a parent tag in the form tag1/tag2/tag3 in the post variables to show only that subtree.
	* Also accepts a state (collapsed or expanded) in the post variables.
	*/
	public function action_tree()
	{
		$parent = Arr::get( Request::current()->post(), 'parent', 'tags' );
		$parent = ORM::factory( 'tag' )->find_by_route( $parent );
		$tags = DB::select( array( 'tag.id', 'tag_id' ), 'version.name', 'tag_mptt.*' )
					->from( 'tag' )
					->join( array( 'tag_v', 'version'), 'inner' )
					->on( 'active_vid', '=', 'version.id' )
					->join( 'tag_mptt' )
					->on( 'tag_mptt.id', '=', 'tag.id' )
					->where( 'version.deleted', '=', false )
					->where( 'scope', '=', $parent->mptt->scope )
					->where( 'lft', '>=', $parent->mptt->lft )
					->where( 'rgt', '<=', $parent->mptt->rgt )
					->order_by( 'lft', 'asc' )
					->execute()->as_array();

		$v = View::factory( 'cms/ui/tags/tree' );
		$v->tags = $tags;
		$v->state = Arr::get( Request::current()->post(), 'state', 'collapsed' );

		$this->response->body( $v );
	}
}

?>