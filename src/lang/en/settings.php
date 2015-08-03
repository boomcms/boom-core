<?php

return [
    'menu' => [
        'navigation' => 'Navigation',
        'urls' => 'URLs',
        'search' => 'Search',
        'tags' => 'Tags',
        'children' => 'Child page settings',
        'admin' => 'Admin',
        'feature' => 'Feature Image',
        'template' => 'Template',
        'visibility' => 'Visibility',
    ],
    'admin' => [
        'heading' => 'Admin settings',
        'internal-name' => 'Internal name',
        'disable-delete' => 'Prevent this page from being deleted?',
    ],
    'advanced' => 'Advanced',
    'basic' => 'Basic',
    'children' => [
        'heading' => 'Child page settings',
        'template' => 'Default child template',
        'order' => 'Child ordering policy',
        'nav' => 'Children visible in nav',
        'nav-cms' => 'Children visible in CMS nav',
        'uri-prefix' => 'Default child URI prefix',
        'grandchild-template' => 'Default grandchild template',
    ],
    'feature' => [
        'heading' => 'Page feature image',
    ],
    'navigation' => [
        'heading' => 'Page navigation settings',
        'nav' => 'Visible in navigation',
        'cms' => 'Visible in CMS navigation',
    ],
    'search' => [
        'heading' => 'Page search settings',
        'description' => 'Description',
        'keywords' => 'Keywords',
        'external' => 'Allow indexing by search engines',
        'internal' => 'Show in site search results',
    ],
    'tags' => [
        'heading' => 'Page tags',
        'pages' => [
            'intro' => "<p>You can other pages as being related to this page.</p>
                        <p>This might be useful, for example, when your site contains blog templates and you need to define a relationship between blog posts and author pages.</p>
                        <p>In this case you would tag a blog post with author pages.</p>",
            'add' => 'Add related page',
            'current' => 'Current page relationships:',
        ],
    ],
    'template' => [
        'heading' => 'Page template',
    ],
    'visibility' => [
        'heading' => 'Page visibility',
        'visible' => 'Visible',
        'from' => 'Visible from',
        'to' => 'Visible until',
    ]
];