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
        'drafts' => 'Draft status',
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
    'draft-status' => [
        'heading' => 'Draft status',
        'intro' => 'When you edit the template or content of a page your changes will be saved as a draft and won\'t appear on the live site until published.',
        'draft' => 'The latest version of this page is in <strong>draft</strong> and <strong>will not be visible on the live site</strong>',
        'pending' => 'The latest version of this page is current <strong>pending approval</strong> by someone with publish permissions for this page',
        'embargoed' => 'The latest version of this page is <strong>embargoed</strong> and will become published at <strong>:date</strong>',
        'published' => 'There are no drafts for this page. All edits are <strong>published</strong>',
        'latest' => 'This version was created by :name (:email) at :date',
        'published-since' => 'and has been published since :date',
        'last-published' => 'The page was last published at :date',
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