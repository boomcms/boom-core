<?php

/**
* Tag MPTT tree.
*
* We use a 3rd party Kohana module to handle mptt trees.
* @see https://github.com/evopix/orm-mptt
*
* Table name: tag_mptt
* 
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description		
****	id				****	integer		****	Primary key. auto increment.			
****	lft				****	integer		****	The tag's MPTT left value (left is a reserved word).
****	rgt				****	integer		****	The tag's MPTT right values (right is a reserved word).
****	parent_id		****	integer		****	MPTT ID of the parent tag. Used to recalculate the tree.
****	lvl				****	integer		****	The tag's level in the tree.
****	scope			****	integer		****	This appears to allow multiple trees to be stored in the same table.
******************************************************************
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Tag_Mptt extends ORM_MPTT
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'tag_mptt';
	protected $_has_one = array( 'tag' => array( 'foreign_key' => 'id' ) );	
	
}


?>
