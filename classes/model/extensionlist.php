<?php

/**
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class extensionlist_Model extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $table_name = 'extensionlist';
	protected $has_one = array( 
		'version'	=> array('model' => 'extensionlist_v' ),
	);
	protected $has_many = array( 
		'versions'	=> array('model' => 'extensionlist_v', 'foreign_key' => 'id')
	);
	protected $belongs_to = array('model' => 'asset');
	protected $load_with = array( 'version' );
	protected $foreign_key = array( 'version' => 'active_vid' );
}

?>