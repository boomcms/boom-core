<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Model_Group extends ORM_Versioned
{
	protected $_table_name = 'groups';

	protected $_belongs_to = array( 
		'version'  => array( 'model' => 'version_group', 'foreign_key' => 'active_vid' ), 
	);
	
	protected $_load_with = array( 'version' );
}


?>