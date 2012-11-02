<?php defined('SYSPATH') OR die('No direct script access.');

/**
*
* @package	Sledge
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
* 
*/
class Sledge_Model_Person_Group extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_db_group = 'default';

	protected $_table_columns = array(
		'person_id'	=>	'',
		'group_id'	=>	'',
		'id'		=>	'',
	);
}