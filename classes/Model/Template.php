<?php

class Model_Template extends ORM
{
	protected $_table_columns = array(
		'id'			=>	'',
		'name'		=>	'',
		'description'	=>	'',
		'filename'		=>	'',
	);

	protected $_table_name = 'templates';

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
			),
			'filename' => array(
				array('not_empty'),
			),
		);
	}
}