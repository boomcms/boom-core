<?php defined('SYSPATH') OR die('No direct script access.');

/**
*
* @package	Sledge
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
* 
*/
class Sledge_Model_Group_Version extends Model_Version
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_has_one = array(
		'group'	=> array('model' => 'group', 'foreign_key' => 'id'),
	);
	protected $_table_columns = array(
		'id'	=>	'',
		'rid'	=>	'',
		'name'	=>	'',
		'audit_person'	=>	'',
		'audit_time'	=>	'',
		'deleted'	=>	'',
	);
	
	/**
	* ORM Validation rules
	* @link http://kohanaframework.org/3.2/guide/orm/examples/validation
	*/
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
			),
		);
	}	
}