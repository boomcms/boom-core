<?php

/**
* Class to represent a row from the page_permission table.
* This table stores permissions for the page as a whole. For permissions on specific page properties see {@link Model_Page_Permission_Property}
* Permissions use bitwise operators. If you're confused have a sticky beak at {@link http://www.php4every1.com/tutorials/create-permissions-using-bitwise-operators-in-php}
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Permission_Page extends ORM 
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'permissions_page';
	protected $_has_one = array( 
		'person'	=> array(),
		'page'		=> array()
	);
	
	
	/**
	* Read constant
	*
	* @var int
	*/
	const READ = 1;
	
	/**
	* Edit page constant
	*
	* @var int
	*/
	const EDIT = 2;	
	
	/**
	* Add page constant
	*
	* @var int
	*/
	const ADD = 4;
	
	/**
	* Clone constant
	*
	* @var int
	*/
	const CLONE = 8;
	
	/**
	* Publish constant
	*
	* @var int
	*/
	const PUBLISH = 16;
	
	/**
	* Delete constant
	*
	* @var int
	*/
	const DELETE = 32;
	
	/**
	* Determines whether a user has the required permissions for a page action.
	*
	* @param integer $what Desired action
	* @param Model_Page $page The page we want to perform the action on.
	* @param Model_Person $person The person trying to do the action
	* @return boolean true if they can, false if they can't
	*
	* @example Model_Page_Permission::may_i( Model_Page_Permission::EDIT, $page, $person );
	*/
	public static may_i( $what, Model_Page $page, Model_Person $person )
	{
		// Find the page's position in the tree.
		$left_val = $page->mptt->left_val;
		
		// Find the lowest level person permission.
		$permission = ORM::factory( class_name( self ) )
			->join( 'page_mptt', 'page_id', 'page_id' )
			->where( 'person_id', '=', $person->id )
			->and_where( 'page_id', '=', $page->id )
			->and_where( 'mptt_left', '<', $page->mptt->left_val )
			->and_where( 'mptt_right', '>', $page->mptt->right_val )
			->order_by( 'mptt_left', 'asc' )
			->limit( 1 )
			->find();
			
		// If nothing was loaded there's no permissions for this user set in the tree.
		//Just a hunch but I reckon they don't have permission for what they're trying to do.
		if (!$permission->loaded())
			return false;
		
		if ($permission->permission & $what)
			return true;
		else
			return false;
	}
}


?>
