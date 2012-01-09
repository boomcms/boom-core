<?php

/**
* Linkset links model
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
	
	protected $_has_one = array(
		'page'	=> array( 'model' => 'page', 'foreign_key' => 'id')
	);
}

?>