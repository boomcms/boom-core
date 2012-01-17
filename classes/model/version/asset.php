<?php

/**
* Asset Model
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Version_Asset extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'asset_v';	
	protected $_has_one = array(
		'asset'	=> array( 'model' => 'asset', 'foreign_key' => 'id' ),
	);
	protected $_belongs_to = array( 
		'person'			=> array( 'model' => 'person', 'foreign_key' => 'id' ),
	);


}
?>
