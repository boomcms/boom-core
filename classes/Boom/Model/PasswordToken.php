<?php

class Boom_Model_PasswordToken extends ORM
{
	protected $_belongs_to = array('person' => array());
	protected $_table_name = 'password_tokens';

	protected $_table_columns = array(
		'id' => '',
		'person_id' => '',
		'token' => '',
		'created' => '',
		'expires' => '',
	);

	protected $_created_column = array(
		'column'	=>	'created',
		'format'	=>	TRUE,
	);

	public function is_expired()
	{
		return $this->expires > $_SERVER['REQUEST_TIME'] + Date::HOUR;
	}
}