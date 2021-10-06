<?php

return [
    'assets' => [
        'url'  => '/boomcms/asset-manager',
        'role' => 'manageAssets',
        'icon' => 'picture-o',
        'break' => 1,
    ],
    
    'asset-upload' => [
        'url'  => '/boomcms/asset-manager/upload',
        'role' => 'uploadAssets',
        'icon' => 'upload',
        'break' => 0,
    ],

    'asset-metrics' => [
        'url'  => '/boomcms/asset-manager/metrics',
        'role' => 'metricAssets',
        'icon' => 'bar-chart',
        'break' => 0,
    ],

    'pages' => [
        'url'  => '/boomcms/page-manager',
        'role' => 'managePages',
        'icon' => 'sitemap',
        'break' => 1,
    ],
    'templates' => [
        'url'  => '/boomcms/template-manager',
        'role' => 'manageTemplates',
        'icon' => 'file-o',
        'break' => 0,
    ],
    
    'people' => [
        'url'  => '/boomcms/people-manager',
        'role' => 'managePeople',
        'icon' => 'users',
        'break' => 1,
    ],
    'settings' => [
        'url'  => '/boomcms/settings',
        'role' => 'manageSettings',
        'icon' => 'cog',
        'break' => 0,
    ],
];
