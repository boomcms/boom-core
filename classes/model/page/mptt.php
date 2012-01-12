<?php

/**
* This model handles page MPTT records and associated methods. We also have an MPTT library. The library has functions which relate to the MPTT tree as a whole - this model should be used in relation to a particular record in the MPTT tree.
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* @link http://www.sitepoint.com/hierarchical-data-database-2/
* @todo MPTT trees currently don't support child order policies.
*
*/
class Model_Page_Mptt extends ORM_MPTT {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'page_mptt';
	protected $_belongs_to = array( 'page' => array( 'foreign_key' => 'page_id' ) );	
	
}


?>
