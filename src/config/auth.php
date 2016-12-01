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
            'driver' => 'boomcms',
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
