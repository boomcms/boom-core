<?php

use Boom\Person;

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

    /**
     *
     * @var Person
     */
    private $person;

    public function is_expired()
    {
        return $this->expires > $_SERVER['REQUEST_TIME'] + Date::HOUR;
    }

    /**
     *
     * @return Person\Person
     */
    public function getPerson()
    {
        if ($this->person === null) {
            $this->person = Person\Factory::byId($this->person_id);
        }

        return $this->person;
    }
}
