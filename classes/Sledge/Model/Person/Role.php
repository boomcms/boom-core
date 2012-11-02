<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Link table for the one-to-many relationship between people and roles.
 *
 * @package	Sledge
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Sledge_Model_Person_Role extends ORM
{
	protected $_db_group = 'default';

	protected $_table_columns = array(
		'id'			=>	'',
		'person_id'	=>	'',
		'role_id'		=>	'',
		'group_id'		=>	'',
		'page_id'		=>	'',
		'allowed'		=>	'',
	);
}