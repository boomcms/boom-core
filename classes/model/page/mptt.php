<?php

/**
* We use a 3rd party Kohana module to handle mptt trees.
* @see https://github.com/evopix/orm-mptt
*
* Table name: page_mptt
* 
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	page_id			****	integer		****	ID of the page the MPTT values belong to.
****	lft				****	integer		****	The page's MPTT left value (left is a reserved word).
****	rgt				****	integer		****	The page's MPTT right values (right is a reserved word).
****	parent_id		****	integer		****	MPTT ID of the parent page. Used to recalculate the tree.
****	lvl				****	integer		****	The page's level in the tree.
****	scope			****	integer		****	This appears to allow multiple trees to be stored in the same table.
****	id				****	integer		****	Primary key. auto increment.
******************************************************************
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* @see http://www.sitepoint.com/hierarchical-data-database-2/
* @see
*
*/
class Model_Page_Mptt extends ORM_MPTT {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'page_mptt';
	protected $_belongs_to = array( 'page' => array( 'foreign_key' => 'id' ) );	
	
}


?>
