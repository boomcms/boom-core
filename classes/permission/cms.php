<?php

/**
*
* @package Permissions
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Permission_Cms extends Permission
{
	public function __construct( Model_Person $person )
	{
		parent::__construct( $person );
		
		$this->_query->join( 'actions', 'inner' )
					->on( 'permissions.action_id', '=', 'actions.id' );
	}
	/**
	* Determines whether the person can do the desired CMS action.
	* In this case $what is ignored - we just check whether they have access to the desired action
	*/
	public function can( $action )
	{
		$this->_query->where( 'actions.name', '=', $action );
		$this->_result = $this->_query->execute();	
			
		return (bool) $this->_result->get( 'permission' );
	}
}

?>