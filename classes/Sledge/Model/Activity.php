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
class Sledge_Model_Activity extends ORM
{
	protected $_table_columns = array(
		'id'			=>	'',
		'remotehost'	=>	'',
		'description'	=>	'',
		'note'		=>	'',
		'person'		=>	'',
		'time'		=>	'',
	);
}