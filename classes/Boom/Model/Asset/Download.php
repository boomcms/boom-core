<?php

namespace Boom\Model\Asset;

class Download extends \ORM
{
	protected $_belongs_to = array(
		'asset'		=>	array(),
	);

	protected $_created_column = array(
		'column'	=>	'time',
		'format'	=>	true,
	);

	protected $_table_columns = array(
		'id' => '',
		'asset_id' => '',
		'time' => '',
		'ip' => '',
	);

	protected $_table_name = 'asset_downloads';
}