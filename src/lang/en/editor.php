<?php

return [
    'calendar' => [
        'info' => 'Select a date and add associated text',
    ],
    'html' => [
        'heading'  => 'Insert some HTML below to embed in the page',
        'info'     => 'You will need to preview the page in order to view the HTML as it will appear in the site',
        'editlink' => 'Edit HTML chunk',
    ],
    'link-picker' => [
        'tabs'            => [
            'internal' => 'Select page',
            'external' => 'Enter URL',
            'asset'    => 'Link to asset',
            'text'     => 'Link text',
        ],
        'internal'        => 'Select a page from the CMS page tree',
        'external'        => 'Manually enter an internal or external URL, or type the title of a page to search for a URL',
        'text'            => 'Set the text for the link',
        'asset-action'    => 'What should happen when this link is clicked?',
        'action-view'     => 'The asset should be viewed',
        'action-download' => 'The asset should be downloaded',
    ],
    'timestamp' => [
        'intro'  => 'Select a date / time and format below',
        'format' => 'Format',
        'value'  => 'Date / time',
    ],
    'conflict' => [
        'exists'  => 'Your changes have not been saved to avoid overwriting changes made by another user.',
        'options' => 'Do you want to discard your changes, overwrite the latest changes, or load the latest changes in a new window to review the situation?',
    ],
    'chunk' => [
        'library' => [
            'heading'     => 'Asset Library',
            'about'       => 'Specify criteria to display assets by',
            'limit'       => 'Number of assets to show',
            'limit_about' => 'Set an (optional) limit on the number of results to be returned. Leave empty for no limit.',
        ],
        'location' => [
            'details'        => 'Location details',
            'title'          => 'Title',
            'address'        => 'Address',
            'search-address' => 'Search address',
            'set-location'   => 'Set location',
            'lat'            => 'Latitude',
            'lng'            => 'Longitude',
            'map'            => 'Map',
            'map-desc'       => 'Set the location by clicking a point on the map.<br />Drag the marker to change the location.',
        ],
    ],
];
