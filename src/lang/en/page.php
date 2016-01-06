<?php

use BoomCMS\Database\Models\Page;

return [
    'add' => [
        'heading'  => 'Add page',
        'question' => 'Do you want to add the new page as a child or a sibling of the current page?',
        
    ],
    'add-behaviour' => [
        Page::ADD_PAGE_PROMPT  => 'Display a prompt',
        Page::ADD_PAGE_CHILD   => 'Add a child page',
        Page::ADD_PAGE_SIBLING => 'Add a sibling page',
    ],
    'visible'   => 'Visible',
    'invisible' => 'Invisible',
    'urls'      => [
        'move' => [
            'heading'         => 'Move URL',
            'primary'         => 'This URL is the primary URL for its page. If you move this URL its current page may become inaccessible.',
            'deleted-warning' => 'This URL is assigned to a page which has been deleted.',
            'deleted'         => '(deleted)',
            'current'         => 'Current Page',
            'new'             => 'New Page',
        ],
    ],
];
