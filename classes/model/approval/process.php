<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/

class Model_Approval_Process extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'approval_process';
	
	protected $_has_one = array( 'version' => array('model' => 'version_approval_process', 'foreign_key' => 'id' ));
	protected $_belongs_to = array( 'version_page' => array( 'model' => 'version_page', 'foreign_key' => 'approval_process_id' ) );
	protected $_load_with = array( 'version' );	
	
	
	
}