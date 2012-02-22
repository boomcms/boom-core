<?php

/**
* Linkset links model
*
* Table name: linksetlinks
*
*************************** Table Columns ************************
****	Name				****	Data Type	****	Description					
****	id					****	integer		****	Primary key, auto increment
****	target_page_id		****	integer		****	Page ID of the linked to page, if this is an internal link.
****	chunk_linkset_id	****	integer		****	The ID of the chunk_linkset that this link belongs to.
****	url					****	string		****	A URL if this is an external link.
****	title 				****	string		****	Link title. What goes between the a tag, e.g. <a href='{url}'>{title}</a>
******************************************************************
*
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_LinksetLink extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'linksetlinks';	
	
	protected $_belongs_to = array(
		'target'	=> array( 'model' => 'page', 'foreign_key' => 'target_page_id')
	);
}

?>