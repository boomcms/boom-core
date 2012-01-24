<?php

/**
*
* @package Permissions
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Permission_Page_Property extends Permission_Page
{
	protected function __construct( Model_Person $person, $page, $property )
	{
		parent::__construct( $person, $page );
		
		$this->_query->join( 'actions', 'inner' )
					->on( 'permissions.action_id', '=', 'actions.id' )
					->or_where_open()
					->or_where( 'actions.name', '=', "page $property" )
					->or_where( 'actions.name', '=', 'page all' )
					->or_where_close();
	}
}

?>