<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Logs actions performed in the CMS.
*
* @package	Sledge
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Log extends ORM
{
	protected $_table_columns = array(
		'id'			=>	'',
		'remotehost'	=>	'',
		'description'	=>	'',
		'note'		=>	'',
		'person_id'	=>	'',
		'time'		=>	'',
	);

	protected $_table_name = 'logs';
}