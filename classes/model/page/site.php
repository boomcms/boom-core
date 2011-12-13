<?php

/**
*
* @package Page
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Page_Site extends Model_Page {

	/**
	*
	* Used to enforce requirements for hiding certain pages from the site view (e.g. invisible or unpublished)
	* Calls parent::__construct() to actually get the page (which hasn't been deleted) and performs additional checks for visibility and publishedness. 
	* Also checks that the parent page is visible and published.
	*
	* @param mixed $key Unique key referring to the page in the database. May be a page ID (int) or URI (string).
	* @return page_Model|false Returns an instance of a page if it exists or false if not found (or deleted).
	*
	*/
	function __construct( $key = false ) {
		$page = parent::__construct( $key );
		
		if ($key === false)
			return $page;
		
		if (!$page instanceof self)
			return false;
			
		// Do visibility and published checking.
		if ($page->isVisible() && $page->isPublished() && $page->getParent()->isVisible() && $page->getParent()->isPublished())
			return $page;
		else
			return false;		
	}
	
	
}


?>
