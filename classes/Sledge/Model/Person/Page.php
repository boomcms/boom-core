<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Stores which page a user in the CMS is currently viewing.
 *
 * @package Sledge
 * @category Models
 *
 */
class Sledge_Model_Person_Page extends ORM
{
	protected $_db_group = 'default';
	protected $_table_name = 'people_pages';
	protected $_table_columns = array(
		'person_id'		=>	'',
		'page_id'			=>	'',
		'since'			=>	'',
		'last_active'		=>	'',
		'saved'			=>	FALSE,
	);

	protected $_cache_columns = array('person_id');
}