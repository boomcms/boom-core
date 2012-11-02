<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * 
* @package	Sledge
* @category	Models
* @category	Permissions
* @author	Rob Taylor
* @copyright	Hoop Associates
*/
class Sledge_Model_Role extends ORM
{
	protected $_table_columns = array(
		'id'			=>	'',
		'name'		=>	'',
		'description'	=>	'',
	);

	protected $_cache_columns = array("name");
}