<?php defined('SYSPATH') OR die('No direct script access.');
/**
 *
* @package	BoomCMS
* @category	Models
* @category	Permissions
* @author	Rob Taylor
* @copyright	Hoop Associates
*/
class Boom_Model_Role extends ORM
{
	protected $_table_columns = array(
		'id'			=>	'',
		'name'		=>	'',
		'description'	=>	'',
	);

	protected $_table_name = 'roles';
}