<?php

namespace BoomCMS\Core\Asset\Helpers;

use Illuminate\Support\Facades\DB;

abstract class Type
{
    const IMAGE = 1;
    const PDF = 2;
    const VIDEO = 3;
    const TIFF = 4;
    const MP3 = 5;
    const MSWORD = 6;
    const MSEXCEL = 7;
    const TEXT = 8;

    public static $supportedMimetypes  = [
        self::IMAGE => [
            'image/jpeg',
            'image/gif',
            'image/png',
        ],
        self::PDF => [
            'application/pdf',
        ],
        self::VIDEO => [
            'video/mp4',
            'video/quicktime',
        ],
        self::TIFF => [
            'image/tiff',
        ],
        self::MP3 => [
            'audio/mpeg',
        ],
        self::MSWORD => [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        self::MSEXCEL => [
            'application/msexcel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
        self::TEXT => [
            'text/plain',
        ],
    ];

    /**
     * @param int
     *
     * @return string
     */
    public static function numericTypeToClass($type)
    {
        switch ($type) {
            case static::IMAGE:
                return 'Image';

            case static::VIDEO:
                return 'Video';

            case static::PDF:
                return 'PDF';

            case static::TIFF:
                return 'Tiff';

            case static::MSWORD:
                return 'MSWord';

            case static::MSEXCEL:
                return 'MSExcel';

            case static::TEXT:
                return 'Text';
        }
    }

    public static function classNameFromMimetype($mime)
    {
        return static::numericTypeToClass(static::typeFromMimetype($mime));
    }

    public static function controllerFromClassname($classname)
    {
        $namespace = 'BoomCMS\Http\Controllers\Asset\\';

        return (class_exists($namespace.$classname)) ? $namespace.$classname : $namespace.'BaseController';
    }

    public static function typeFromMimetype($mime)
    {
        foreach (static::$supportedMimetypes as $type => $mimetypes) {

            if (array_search($mime, $mimetypes) !== false) {
                return $type;
            }
        }
    }
}
