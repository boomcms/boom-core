<?php

class Boom_Model_AuthLog extends ORM
{
	protected $_table_columns = array(
		'id'			=>	'',
		'person_id'		=>	'',
		'action'		=>	'',
		'method'		=>	'',
		'ip'			=>	'',
		'user_agent'	=>	'',
		'time'			=>	'',
	);

	protected $_table_name = 'auth_log';

	protected $_created_column = array(
		'column'	=>	'time',
		'format'	=>	TRUE,
	);
}