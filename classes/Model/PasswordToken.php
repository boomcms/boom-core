<?php

class Model_PasswordToken extends ORM
{
    protected $_belongs_to = ['person' => []];
    protected $_table_name = 'password_tokens';

    protected $_table_columns = [
        'id' => '',
        'person_id' => '',
        'token' => '',
        'created' => '',
        'expires' => '',
    ];

    protected $_created_column = [
        'column'    =>    'created',
        'format'    =>    true,
    ];

    public function is_expired()
    {
        return $this->expires > $_SERVER['REQUEST_TIME'] + Date::HOUR;
    }
}
