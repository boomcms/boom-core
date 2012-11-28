<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	Sledge
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Sledge_Model_Page_Version extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_belongs_to = array(
		'template'		=>	array('model' => 'Template', 'foreign_key' => 'template_id'),
		'person'		=>	array('model' => 'Person', 'foreign_key' => 'created_by'),
	);

	protected $_table_columns = array(
		'id'				=>	'',
		'page_id'			=>	'',
		'template_id'		=>	'',
		'title'				=>	'',
		'keywords'		=>	'',
		'description'		=>	'',
		'created_by'		=>	'',
		'created_time'		=>	'',
		'feature_image_id'	=>	'',
	);

	protected $_cache_columns = array('internal_name');

	/**
	* Filters for the versioned person columns
	* @link http://kohanaframework.org/3.2/guide/orm/filters
	*/
	public function filters()
	{
	    return array(
			'title' => array(
				array('html_entity_decode'),
				array('urldecode'),
				array('trim'),
			),
			'keywords' => array(
				array('trim'),
			),
			'description' => array(
				array('trim'),
			),
	   );
	}
}