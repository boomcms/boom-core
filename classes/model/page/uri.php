<?php

/**
*
* @package Page
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* @todo Work out which methods we actually need from hoopbasepagemodel and implement them nicely. Then just extend ORM 
*
*/
class Model_Page_Uri extends ORM_Versioned {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'page_uri';
	//protected $_belongs_to = array( 'page' => array( 'model' => 'page' ) );

	/** 
	* Ensures we can find a page ID by URI through cache.
	*/
	public function save( Validation $validation = NULL ) {
		$return = parent::save();
		
		$cache = Cache::Instance();
		$cache->set( 'page_id_from_uri_' . md5( $this->version->uri ), $this->version->page_id, 'page_uris' );	
		
		return $return;	
	}
}

?>