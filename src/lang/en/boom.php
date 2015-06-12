<?php

return [
    'page' => 'page|pages',

    'chunks' => [
        'text' => [
            'default' => 'Default text',
            'standfirst' => 'Insert standfirst here. A single sentence of around 15 words introducing the page',
            'bodycopy' => '<p>Insert the main content of the page here</p>',
        ],
        'feature' => [
           'default' => 'Feature a page here',
        ],
        'linkset' => [
            'default' => 'Insert a linkset here',
        ],
        'slideshow' => [
            'default' => 'Insert a slideshow',
        ],
        'asset' => [
            'default' => 'Insert an asset',
        ],
        'timestamp' => [
            'default' => 'Select date and / or time',
        ],
        'tag' => [
            'default' => 'Select a tag to feature here',
        ],
    ],
	
    'recover' => [
        'email_sent' => 'We\'ve sent you an email with instructions on how to reset your password',
        'errors' => [
            'invalid_email' => 'Invalid email address',
            'invalid_token' => 'Invaid password reset token.',
            'password_mismatch' => 'The passwords you entered didn\'t match',
        ],
    ],
];