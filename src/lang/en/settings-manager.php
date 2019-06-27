<?php

return [
    '_heading'  => 'Manage BoomCMS settings',
    '_saved'    => 'You\'re settings have been saved',
    'analytics' => [
        '_label' => 'Analytics',
        '_info'  => 'Enter an analytics tracking script here. This can then be inserted into templates when the site is in production',
    ],
    'site' => [
        'admin' => [
            'email' => [
                '_label' => 'Site admin email',
                '_info'  => 'The email address for the site administrator. This will be used as the \'from\' field for any CMS emails.',
            ],
        ],
        'name' => [
            '_label' => 'Site name',
        ],
        'support' => [
            'email' => [
                '_label' => 'Site support email',
                '_info'  => 'Email address that support queries will be sent to',
            ],
        ],
        'languages' => [
            '_label' => 'Site languages',
            '_info' => 'Please choose languages'
        ]
    ],
];
