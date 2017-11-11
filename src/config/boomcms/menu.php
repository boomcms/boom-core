<?php

return [
    'pages' => [
        'url'  => '/boomcms/page-manager',
        'role' => 'managePages',
        'icon' => 'sitemap',
    ],
    'templates' => [
        'url'  => '/boomcms/template-manager',
        'role' => 'manageTemplates',
        'icon' => 'file-o',
    ],
    'assets' => [
        'url'  => '/boomcms/asset-manager',
        'role' => 'manageAssets',
        'icon' => 'picture-o',
    ],
    'asset-upload' => [
        'url'  => '/boomcms/asset-manager#upload',
        'role' => 'uploadAssets',
        'icon' => 'upload',
    ],
    'people' => [
        'url'  => '/boomcms/people-manager',
        'role' => 'managePeople',
        'icon' => 'users',
    ],
    'settings' => [
        'url'  => '/boomcms/settings',
        'role' => 'manageSettings',
        'icon' => 'cog',
    ],
];
