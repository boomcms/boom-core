<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Link table for the one-to-many relationship between people and roles.
 *
 * @package	BoomCMS
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Boom_Model_Person_Role extends ORM
{
	protected $_db_group = 'default';

	protected $_table_columns = array(
		'person_id'	=>	'',
		'role_id'		=>	'',
		'role_id'		=>	'',
		'page_id'		=>	'',
		'allowed'		=>	'',
	);

	protected $_table_name = 'people_roles';
}