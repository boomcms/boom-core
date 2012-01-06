<?php

/**
*
* @package Page
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
	//protected $_belongs_to = array( 'page' => array( 'model' => 'page' ) );
	
	/**
	* Page URI save method.
	* Ensures that a page can only have one primary URI
	*
	* @param Validation $validation Validation rules
	*/
	public function save( Validation $validation = null )
	{
		$return = parent::save( $validation );
		
		if ($this->primaryuri == 't')
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