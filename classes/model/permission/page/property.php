<?php

/**
* Class to represent a row from the page_permission_property table.
* This table stores permissions for individual page properties.
* This is used in the page settings manager of the CMS to restrict access to particular page settings.
*
* Permissions use bitwise operators. If you're confused have a sticky beak at {@link http://www.php4every1.com/tutorials/create-permissions-using-bitwise-operators-in-php}
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Permission_Page_Property extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'permissions_page_property';
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
	* ID for visibleto property
	* @var int
	*/
	const VISIBLETO = 1;
	
	/**
	* ID for indexing property
	* @var int
	*/
	const INDEXING = 2;
	
	/**
	* ID for URIs property
	* @var int
	*/
	const URIS = 3;
	
	/**
	* ID for default child uri prefix property
	* @var int
	*/
	const DEFAULT_CHILD_URI_PREFIX = 4;
	
	/**
	* ID for page status property
	* @var int
	*/
	const PAGE_STATUS = 5;
	
	/**
	* ID for parent page property
	* @var int
	*/
	const PARENT = 6;
	
	/**
	* ID for visible in leftnav property
	* @var int
	*/
	const VISIBLE_IN_LEFTNAV = 7;
	
	/**
	* ID for visible_in_leftnav_cms property
	* @var int
	*/
	const VISIBLE_IN_LEFTNAV_CMS = 8;
	
	/**
	* ID for description property
	* @var int
	*/
	const DESCRIPTION = 10;
	
	/**
	* ID forchild ordering policy property
	* @var int
	*/
	const CHILD_ORDERING_POLICY = 11;
	
	/**
	* ID for title property
	* @var int
	*/
	const TITLE = 12;
	
	/**
	* ID for default child template property
	* @var int
	*/
	const DEFAULT_CHILD_TEMPLATE = 13;
	
	/**
	* ID for visible from property
	* @var int
	*/
	const VISIBLEFROM = 14;

	/**
	* ID for categories property
	* @var int
	*/
	const CATEGORIES = 15;
	
	/**
	* ID for child parent property
	* @var int
	*/
	const CHILD_PARENT = 16;
	
	/**
	* ID for keywords property
	* @var int
	*/
	const KEYWORDS = 17;
	
	/**
	* ID for children can have tags property
	* @var int
	*/
	const CHILDREN_CAN_HAVE_TAGS = 19;
	
	/**
	* ID for children hidden from leftnav property
	* @var int
	*/
	const CHILDREN_HIDDEN_FROM_LEFTNAV = 20;
	
	/**
	* ID for rss property
	* @var int
	*/
	const RSS = 21;	
	
	/**
	* ID for hidden_from_search_results property
	* @var int
	*/
	const HIDDEN_FROM_SEARCH_RESULTS = 22;	
	
	/**
	* ID for child_child_parent_page
	* @var int
	*/
	const CHILD_CHILD_PARENT_PAGE = 23;
		
	/**
	* Determines whether a user has the required permissions to view or edit a page property.
	*
	* @param integer $what Desired action
	* @param Model_Page $page The page we want to perform the action on.
	* @param string $property The name of the page property.
	* @param Model_Person $person The person trying to do the action
	* @return boolean true if they can, false if they can't
	*
	* @example Model_Page_Permission_Property::may_i( Model_Page_Permission_Property::EDIT, $page, Model_Page_Permission_Property::VISIBLEFROM, $person );
	*/
	public static may_i( $what, Model_Page $page, $poperty, Model_Person $person )
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
			->and_where( 'property', '=', $property )
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
