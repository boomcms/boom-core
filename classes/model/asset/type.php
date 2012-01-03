<?php

/**
* Asset Model
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Asset_Type extends ORM_Versioned {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'asset_type';
	protected $has_one = array( 
		'mimetype'			=> array('model' => 'mimetypelist' ),
		'extension'			=> array('model' => 'extensionlist' )
	);
}

?>