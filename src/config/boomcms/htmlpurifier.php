<?php

return [
    'htmlpurifier' => [
        'AutoFormat.AutoParagraph' => true,
        'AutoFormat.RemoveEmpty.RemoveNbsp' => true,
        'AutoFormat.RemoveEmpty' => true,
        'AutoFormat.RemoveSpansWithoutAttributes' => true,
        'Core.RemoveInvalidImg' => false,
        'Cache.SerializerPath' => storage_path() . '/boomcms',
        'CSS.AllowedProperties' => [],
        'URI.AllowedSchemes' =>  [
            'http' => true,
            'https' => true,
            'mailto' => true,
            'tel' => true,
            'hoopdb' => true,
            'ftp' => true,
        ],
    ],
];