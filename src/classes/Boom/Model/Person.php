<?php

namespace Boom\Model;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    const LOCK_WAIT = 600;

    protected $table = 'people';

    protected $_table_columns = [
        'id'            =>    '',
        'name'        =>    '',
        'email'        =>    '',
        'enabled'        =>    '',
        'password'        =>    '',
        'failed_logins'    =>    '',
        'locked_until'    =>    '',
        'avatar_id'    =>    '',
        'reset_password_code' => '',
        'persist_code' => '',
    ];

    protected $_belongs_to = [
        'avatar' => ['model' => 'Asset', 'foreign_key' => 'avatar_id'],
    ];

    protected $_has_many = [
        'groups'        => [
            'model'    => 'Group',
            'through'    => 'people_groups',
        ],
        'logs' => [],
        'auth_logs' => ['model' => 'AuthLog'],
    ];

    public function filters()
    {
        return [
            'email' => [
                ['strtolower'],
            ]
        ];
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
