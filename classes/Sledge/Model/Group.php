<?php defined('SYSPATH') OR die('No direct script access.');

/**
*
* @see	[Model_Group_Version]
* @package	Sledge
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Group extends ORM_Versioned
{
	protected $_belongs_to = array(
		'version'  => array('model' => 'Group_Version', 'foreign_key' => 'active_vid'),
	);
	protected $_has_many = array('roles' => array('through' => 'group_roles'));

	protected $_load_with = array('version');
	protected $_table_columns = array(
		'id'			=>	'',
		'active_vid'	=>	'',
	);
}
