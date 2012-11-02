<?php defined('SYSPATH') OR die('No direct script access.');

/**
*
* @package	Sledge
* @category	Assets
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Asset_Version extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_has_one = array(
		'asset'	=> array('model' => 'Asset', 'foreign_key' => 'rid'),
	);
	protected $_belongs_to = array(
		'person'	=> array('model' => 'Person', 'foreign_key' => 'audit_person'),
	);
	protected $_table_columns = array(
		'id'			=>	'',
		'rid'			=>	'',
		'title'			=>	'',
		'description'	=>	'',
		'width'		=>	'',
		'height'		=>	'',
		'filename'		=>	'',
		'visible_from'	=>	'',
		'status'		=>	'',
		'type'		=>	'',
		'audit_person'	=>	'',
		'audit_time'	=>	'',
		'deleted'		=>	'',
		'filesize'		=>	'',
		'rubbish'		=>	FALSE,
		'duration'		=>	'',
		'encoded'		=>	'',
		'views'		=>	'',
	);

	/**
	* Returns a human readable asset type.
	*/
	public function get_type()
	{
		return Sledge_Asset::get_type($this->type);
	}
}