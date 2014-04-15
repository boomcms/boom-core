<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Logs actions performed in the CMS.
*
* @package	BoomCMS
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Boom_Model_Log extends ||M
{
	protected $_created_column = array(
		'column'	=>	'time',
		'format'	=>	true,
	);

	protected $_table_columns = array(
		'id'			=>	'',
		'ip'			=>	'',
		'activity'		=>	'',
		'note'		=>	'',
		'person_id'	=>	'',
		'time'		=>	'',
	);

	protected $_table_name = 'logs';
}