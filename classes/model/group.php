<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Model_Group extends ORM
{
	protected $_has_one = array( 
		'version'	=> array( 'model' => 'version_person', 'foreign_key' => 'id' )
	);
	protected $_has_many = array( 
		'versions'	=> array( 'model' => 'version_person', 'foreign_key' => 'id' ),
		'people'	=> array( 'model' => 'person', 'through' => 'user_group' )	
	);
	

}


?>