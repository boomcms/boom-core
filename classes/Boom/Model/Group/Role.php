<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Keeps a record of which roles are assigned to each group.
 * This table isn't actually used in calculating a person's permissions but acts as a record of which roles have been given to a group so that these can be shown to an editor in the person.
 *
 *
 * @package	BoomCMS
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Boom_Model_Group_Role extends ORM
{
	protected $_db_group = 'default';

	protected $_belongs_to = array('role' => array());

	protected $_table_columns = array(
		'id'			=>	'',
		'group_id'		=>	'',
		'page_id'		=>	'',
		'role_id'		=>	'',
		'allowed'		=>	'',
	);
}