<?php

return [
    'menu' => [
        'close'      => 'Close settings window',
        'delete'     => 'Delete page',
        'navigation' => 'Navigation',
        'urls'       => 'URLs',
        'search'     => 'Search',
        'tags'       => 'Tags',
        'children'   => 'Child page settings',
        'admin'      => 'Admin',
        'feature'    => 'Feature Image',
        'template'   => 'Template',
        'visibility' => 'Visibility',
        'drafts'     => 'Draft status',
        'relations'  => 'Relationships',
    ],
    'admin' => [
        'heading'               => 'Admin settings',
        'internal-name'         => 'Internal name',
        'disable-delete'        => 'Prevent this page from being deleted?',
        'add-behaviour'         => 'What should the \'add page\' button do on this page?',
        'child-add-behaviour'   => 'What should the \'add page\' button do on the children of this page?',
        'add-behaviour-heading' => 'Add page button behaviour',
        'add-behaviour-desc'    => 'You can configure the behaviour of the add page button for this page and its children. This is useful on, for example, a blog landing page to ensure that new pages are always created as children of the landing page.',
    ],
    'advanced' => 'Advanced',
    'basic'    => 'Basic',
    'children' => [
        'heading'             => 'Child page settings',
        'template'            => 'Default child template',
        'order'               => 'Child ordering policy',
        'nav'                 => 'Children visible in nav',
        'nav-cms'             => 'Children visible in CMS nav',
        'uri-prefix'          => 'Default child URI prefix',
        'grandchild-template' => 'Default grandchild template',
    ],
    'delete' => [
        'heading'  => 'Delete page',
        'intro'    => 'Are you sure you want to delete this page? This cannot be undone.',
        'disabled' => 'You cannot delete this page because deletion has been disabled.',
        'children' => [
            'heading'         => 'Page Children',
            'intro'           => 'What action should be taken with this page\'s :count children?',
            'delete'          => 'Also delete the child pages',
            'reparent'        => 'Assign these pages to a new parent',
            'reparent-target' => 'New parent page: ',
        ],
        'urls'     => [
            'heading'              => 'Page URLs',
            'intro'                => 'Set the behaviour of this page\'s URLs after the page is deleted.',
            'leave'                => 'Don\'t redirect the URLs to a new page.',
            'leave-explanation'    => 'This will return a \'page not found\' error if the link is used but is recommended if no other page has content which is relevant to the content of this page.',
            'redirect'             => 'Redirect the URLs to another page',
            'redirect-explanation' => 'This is recommended if another page has content which is relevant to content of this page.',
            'redirect-target'      => 'Redirect URLs to: ',
        ],
    ],
    'draft-status' => [
        'heading'        => 'Draft status',
        'intro'          => 'When you edit the template or content of a page your changes will be saved as a draft and won\'t appear on the live site until published.',
        'draft'          => 'The latest version of this page is in <strong>draft</strong> and <strong>will not be visible on the live site</strong>',
        'pending'        => 'The latest version of this page is current <strong>pending approval</strong> by someone with publish permissions for this page',
        'embargoed'      => 'The latest version of this page is <strong>embargoed</strong> and will become published at <strong>:date</strong>',
        'published'      => 'There are no drafts for this page. All edits are <strong>published</strong>',
        'latest'         => 'This version was created by :name (:email) on :date at :time',
        'last-published' => 'The page was last published on :date at :time',
    ],
    'feature' => [
        'heading'   => 'Page feature image',
        'from-page' => 'Use an image from the page',
    ],
    'navigation' => [
        'heading'   => 'Page navigation settings',
        'nav'       => 'Visible in navigation',
        'cms'       => 'Visible in CMS navigation',
        'no-parent' => 'This page has no parent',
        'parent'    => 'Parent page',
    ],
    'relations'   => [
        'heading' => 'Page relationships',
        'intro'   => '<p>You can define relationships between pages.</p>
                    <p>This might be useful, for example, when your site contains blog templates and you need to define a relationship between blog posts and author pages.</p>
                    <p>In this case you would add author pages as related pages of the blog post page.</p>',
        'current' => 'Current page relationships:',
    ],
    'search' => [
        'heading'     => 'Page search settings',
        'description' => 'Description',
        'keywords'    => 'Keywords',
        'external'    => 'Allow indexing by search engines',
        'internal'    => 'Show in site search results',
    ],
    'tags' => [
        'heading'               => 'Page tags',
        'free'                  => '[Free tags]',
        'new-group-placeholder' => 'New group name',
        'new-tag-placeholder'   => 'New tag name',
        'new-group'             => 'Add a new tag group',
    ],
    'template' => [
        'heading'     => 'Page template',
        'default'     => 'Select a template to use in this page',
        'about'       => 'Changing the template of the page will change how the content of the page is visually displayed.</p><p>Although some content may not be visible with certain templates, the content will remain with the page and become visible if the template is changed back.',
        'template'    => 'Template:',
        'description' => 'Template description:',
        'count'       => 'Pages using this template:',
    ],
    'visibility' => [
        'heading'          => 'Page visibility',
        'visible'          => 'Visible',
        'from'             => 'Visible from',
        'from-description' => 'Set a time and date here to make the page become visible at a particular time',
        'to'               => 'Visible until',
        'to-description'   => 'Set a time and date here to make the page become invisible again in the future',
        'preview'          => 'This page is not visibile. To see how the page would appear on the site you can <a href="#" class="b-visibility-preview">preview the page</a>.',
    ],
];
