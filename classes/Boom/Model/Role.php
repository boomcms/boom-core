<?php

namespace Boom\Model;

class Role extends \ORM
{
	protected $_table_columns = array(
		'id'			=>	'',
		'name'		=>	'',
		'description'	=>	'',
	);

	protected $_table_name = 'roles';
}