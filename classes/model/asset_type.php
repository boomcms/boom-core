<?php

/**
* Asset Model
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class asset_type_Model extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $table_name = 'asset_type';
	protected $has_one = array( 
		'version'	=> array('model' => 'asset_type_v' ),
		'mimetype'			=> array('model' => 'mimetypelist' ),
		'extension'			=> array('model' => 'extensionlist' )
	);
	protected $has_many = array( 
		'versions'	=> array('model' => 'asset_v', 'foreign_key' => 'id')
	);
	protected $load_with = array( 'version' );
	protected $foreign_key = array( 'version' => 'active_vid', 'mimetype' => 'id', 'extension' => 'id' );
}

?>