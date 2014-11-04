<?php

class Model_AuthLog extends ORM
{
    const LOGOUT = -1;
    const FAILURE = 0;
    const LOGIN = 1;

    protected $_table_columns = [
        'id'            =>    '',
        'person_id'        =>    '',
        'action'        =>    '',
        'method'        =>    '',
        'ip'            =>    '',
        'user_agent'    =>    '',
        'time'            =>    '',
    ];

    protected $_table_name = 'auth_logs';

    protected $_created_column = [
        'column'    =>    'time',
        'format'    =>    true,
    ];

    public function get_action()
    {
        switch ($this->action) {
            case static::LOGIN:
                return 'Login';
            case static::LOGOUT:
                return 'Logout';
            case static::FAILURE:
                return 'Login failure';
        }
    }
}
