<?php

return [
    'menu'    =>    [
        'view_filename'        =>    'boom::menu.boom',
        'items'            =>    [
            'home'        =>    [
                'title'        =>    'Site',
                'url'        =>    '/',
                'priority'    =>    1,
            ],
            'pages' => [
                'title' => 'Pages',
                'url' => '/cms/pages',
                'priority' => 5,
                'role' => 'manage_pages',
            ],
            'approvals'    =>    [
                'title'        =>    'Pages pending approval',
                'url'        =>    '/cms/approvals',
                'priority'    =>    10,
                'role'        =>    'manage_approvals',
            ],
            'templates'    =>    [
                'title'        =>    'Templates',
                'url'        =>    '/cms/templates',
                'role'        =>    'manage_templates',
                'priority'    =>    6,
            ],
            'profile'        =>    [
                'title'        =>    'Manage Account',
                'url'        =>    '/cms/account',
                'priority'    =>    99,
            ],
            'logout'        =>    [
                'title'        =>    'Logout',
                'url'        =>    '/cms/logout',
                'priority'    =>    100,
            ],
            'assets'    => [
                'title'        =>    'Assets',
                'url'        =>    '/cms/assets',
                'role'        =>    'manage_assets',
                'priority'    =>    3,
            ],
            'people'    =>    [
                'title'        =>    'People',
                'url'        =>    '/cms/people',
                'role'        =>    'manage_people',
                'priority'    =>    4,
            ],
        ],
    ],
];