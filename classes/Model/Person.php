<?php

class Model_Person extends ORM
{
    const LOCK_WAIT = 600;

    protected $_table_name = 'people';

    protected $_table_columns = array(
        'id'            =>    '',
        'name'        =>    '',
        'email'        =>    '',
        'enabled'        =>    '',
        'password'        =>    '',
        'failed_logins'    =>    '',
        'locked_until'    =>    '',
        'avatar_id'    =>    '',
    );

    protected $_belongs_to = array(
        'avatar' => array('model' => 'Asset', 'foreign_key' => 'avatar_id'),
    );

    protected $_has_many = array(
        'groups'        => array(
            'model'    => 'Group',
            'through'    => 'people_groups',
        ),
        'logs' => array(),
        'auth_logs' => array('model' => 'AuthLog'),
    );

    public function filters()
    {
        return array(
            'email' => array(
                array('strtolower'),
            )
        );
    }

    public function get_lock_wait()
    {
        if ($this->isLocked()) {
            return Date::span($this->locked_until);
        }
    }

    public function get_recent_account_activity()
    {
        return $this
            ->auth_logs
            ->order_by('time', 'desc')
            ->limit(10)
            ->find_all();
    }
}
