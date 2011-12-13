<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/

class approval_process_Model extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $has_one = array( 'current_version' => array('model' => 'approval_process_v'));
	
		
	
	
	
}