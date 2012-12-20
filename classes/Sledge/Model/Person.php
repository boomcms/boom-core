<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package Sledge
 * @category Models
 *
 */
class Boom_Model_Person extends ORM
{
	protected $_table_name = 'people';

	protected $_table_columns = array(
		'id'			=>	'',
		'name'		=>	'',
		'email'		=>	'',
		'enabled'		=>	'',
		'theme'		=>	'',
	);

	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_has_many = array(
		'groups'		=> array(
			'model'	=> 'Group',
			'through'	=> 'people_groups',
		),
		'logs' => array(),
	);
}