<?php

return [
    'pages' => [
        'url'      => '/boomcms/pages',
        'role'     => 'managePages',
        'icon'     => 'sitemap',
    ],
    'approvals' => [
        'url'      => '/boomcms/approvals',
        'role'     => 'manageApprovals',
        'icon'     => 'thumbs-o-up',
    ],
    'templates' => [
        'url'      => '/boomcms/templates',
        'role'     => 'manageTemplates',
        'icon'     => 'file-o',
    ],
    'assets' => [
        'url'      => '/boomcms/assets',
        'role'     => 'manageAssets',
        'icon'     => 'picture-o',
    ],
    'people' => [
        'url'      => route('people-manager'),
        'role'     => 'managePeople',
        'icon'     => 'users',
    ],
    'settings' => [
        'url'      => '/boomcms/settings',
        'role'     => 'manageSettings',
        'icon'     => 'cog',
    ],
];
