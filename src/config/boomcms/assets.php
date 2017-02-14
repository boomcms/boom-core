<?php

return [
    'assets' => [
        'types' => [
            'image' => [
                'image/jpeg',
                'image/gif',
                'image/png',
                'image/tiff',
                'image/svg+xml',
            ],
            'doc' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/msexcel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/zip',
                'application/postscript',
                'text/plain',
            ],
            'video' => [
                'video/mp4',
                'video/quicktime',
            ],
            'audio' => [
                'audio/mpeg',
            ],
        ],
        'extensions' => [
            'eps'  => 'application/postscript',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'jpg'  => 'image/jpeg',
            'tiff' => 'image/tiff',
            'svg'  => 'image/svg+xml',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/msexcel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'txt'  => 'text/plain',
            'mp4'  => 'video/mp4',
            'mpeg' => 'video/quicktime',
            'mp3'  => 'audio/mpeg',
            'zip'  => 'application/zip',
        ],
    ],
];
