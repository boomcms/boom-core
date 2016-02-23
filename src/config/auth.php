<?php

return [
    'defaults' => [
        'guard'     => 'boomcms',
        'passwords' => 'boomcms',
    ],
    'guards' => [
        'boomcms' => [
            'driver'   => 'session',
            'provider' => 'boomcms',
        ],
    ],
    'providers' => [
        'boomcms' => [
            'driver' => 'eloquent',
            'model'  => BoomCMS\Database\Models\Person::class,
        ],
    ],
    'passwords' => [
        'boomcms' => [
            'provider' => 'boomcms',
            'email'    => 'boomcms::email.password',
            'table'    => 'password_resets',
            'expire'   => 60,
        ],
    ],
];
