<?php

return [
    'heading' => 'Existing URLs',
    'help'    => [
        'primary' => 'The highlighted URL indicates the page\'s primary URL',
        'one-primary' => 'You may only have one primary URL for the page which cannot be deleted',
        'redirect' => 'All non-primary URLs will redirect to the primary URL',
        'make-primary' => 'Click on a URL to make it the primary URL for the page',
        'no-edit' => 'There is no \'edit\' URL. Instead of editing a URL you should simply add the new URL. This ensures that the page remains accessible from the existing URL',
    ],
    'add' => [
        'heading'     => 'Add URL',
        'lowercase'   => 'URLs should contain only lower-case letters, numbers, or hyphens eg /course-programe-news-august13',
        'nospaces'    => 'No spaces or punctuation should be used',
        'hyphens'     => 'Separate words with a hyphen eg /case-studies',
        'nosurprises' => 'It should be clear from the URL what the content of the page is. Users who follow a URL to your page shouldn\'t be surprised by the content when they get there',
        'keywords'    => 'Use only keywords in the URL. Remove linking words  – eg  \'and\', \'the\' –, that are not descriptive',
        'new'         => 'New URL',
        'placeholder' => 'Enter new URL here',
    ],
];
