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
	* ORM Validation rules
	* @see http://kohanaframework.org/3.2/guide/orm/examples/validation
	*/
	/*public function rules()
		{
		return array(
			'page_id' => array(
				array('not_empty'),
				array('numeric'),
			),
			'uri' => array(
				array('not_empty'),
				array('max_length', array( ':value', 2048)),
			),
		);
	}*/
    
	public function filters()
	{
		return array(
			'uri' => array(
				array(array($this, 'valid_uri')),
			),
		);
	}
	
	/**
	* Make a URI valid.
	* Remove extra /'s etc.
	*/
	protected function valid_uri( $uri )
	{
		// Make sure it's a uri and not a URL.
		$uri = parse_url( $uri, PHP_URL_PATH);
		
		// Remove a leading '/'
		if (substr( $uri, 0, 1 ) == '/' )
		{
			$uri = substr( $uri, 1 );
		}
		
		// Remove a trailing '/'
		if (substr( $uri, -1, 1 ) == '/' )
		{
			$uri = substr( $uri, 0, -1 );
		}
		
		// Remove duplicate forward slashes.
		$uri = preg_replace( '|/+|', '/', $uri );
		
		// Make sure there's no HTML in there.
		$uri = strip_tags( $uri );
		
		return $uri;
	}
	
	/**
	* Page URI save method.
	* Ensures that a page can only have one primary URI
	*
	* @param Validation $validation Validation rules
	* @throws Sledge_Exception
	*/
	public function save( Validation $validation = null )
	{
		// Check that the URI isn't already in use.
		$existing = DB::select( 'page_id' )->from( 'page_uri' )->where( 'uri', '=', $this->uri )->limit( 1 )->execute();
		
		if ($existing->count() > 0)
		{
			throw new Sledge_Exception( 'URI is already in use' );
			return;
		}
		
		$return = parent::save( $validation );
		
		if ($this->primary_uri == true)
		{
			$query = DB::query( Database::UPDATE, "update page_uri set primary_uri = 'f' where page_id = :page and id != :id " );
			$query->param( ":page", $this->page_id );
			$query->param( ":id", $this->id );
			$query->execute();
			
			Cache::instance()->set( 'primary_uri_for_page:' . $this->page_id );
		}
		
		return $return;		
	}
}

?>
