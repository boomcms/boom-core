<?php defined('SYSPATH') OR die('No direct script access.');

/**
* We use a 3rd party Kohana module to handle mptt trees.
* @link https://github.com/evopix/orm-mptt
*
* @package	Sledge
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
* @link		http://www.sitepoint.com/hierarchical-data-database-2/
*
*/
class Sledge_Model_Page_MPTT extends ORM_MPTT
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'page_mptt';
	protected $_belongs_to = array('page' => array('foreign_key' => 'id'));
	protected $_table_columns = array(
		'id'	=>	'',
		'lft'	=>	'',
		'rgt'	=>	'',
		'parent_id'	=>	'',
		'lvl'	=>	'',
		'scope'	=>	'',
	);
	protected $_reload_on_wakeup = TRUE;
}