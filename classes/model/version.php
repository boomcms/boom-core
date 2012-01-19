<?php

/**
* Sledge base Version model.
* Defines functions for retrieving audit time, etc.
* This model should be extended by all version models.
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
abstract class Model_Version extends ORM {
	
	public function get_time()
	{
		
		return date( "l j F Y H:i", $this->audit_time );
		
	}
	
	
}

?>