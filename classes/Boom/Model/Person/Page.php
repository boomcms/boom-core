<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Stores which page a user in the CMS is currently viewing.
 *
 * @package Boom
 * @category Models
 *
 */
class Boom_Model_Person_Page extends ORM
{
	protected $_table_name = 'people_pages';
	protected $_table_columns = array(
		'person_id'		=>	'',
		'page_id'			=>	'',
		'since'			=>	'',
		'last_active'		=>	'',
		'saved'			=>	FALSE,
	);
}