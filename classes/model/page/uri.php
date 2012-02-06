<?php

/**
* Table name: page_uri
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	id				****	integer		****	Primary key, auto increment
****	page_id			****	string		****	ID of the page the URI belongs to.
****	uri				****	string		****	The URI.
****	primary_uri		****	boolean		****	Whether this is the primary URI for the page.
******************************************************************
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* @todo Work out which methods we actually need from hoopbasepagemodel and implement them nicely. Then just extend ORM 
*
*/
class Model_Page_Uri extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'page_uri';
	protected $_belongs_to = array( 'page' => array( 'model' => 'page', 'foreign_key' => 'page_id' ) );
	
	/**
	* Page URI save method.
	* Ensures that a page can only have one primary URI
	*
	* @param Validation $validation Validation rules
	*/
	public function save( Validation $validation = null )
	{
		$return = parent::save( $validation );
		
		if ($this->primary_uri == true)
		{
			$query = DB::query( Database::UPDATE, "update page_uri set primary_uri = 'f' where page_id = :page and id != :id " );
			$query->param( ":page", $this->page_id );
			$query->param( ":id", $this->id );
			$query->execute();
		}
		
		return $return;		
	}
}

?>