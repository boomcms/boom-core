<?php

namespace BoomCMS\Core\Asset\Mimetype;

abstract class Mimetype
{
    public static $allowedExtensions = ['jpeg', 'gif', 'jpg', 'png', 'tiff', 'doc', 'docx', 'pdf', 'mp4', 'xls', 'xlsx', 'mov'];

    public static $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
        'image/png' => 'png',
        'image/tiff' => 'tiff',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/msexcel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/pdf' => 'pdf',
        'video/mp4' => 'mp4',
        'video/quicktime' => 'mov',
    ];

    protected $_mimetype;

    public function __construct($mimetype)
    {
        $this->_mimetype = $mimetype;
    }

    public function __toString()
    {
        return $this->getMimetype();
    }

    public static function isSupported($mimetype)
    {
        return array_key_exists($mimetype, static::$allowedTypes);
    }

    public static function factory($mimetype)
    {
        if (static::isSupported($mimetype)) {
            $classname = 'BoomCMS\Core\Asset\Mimetype\\' . ucfirst(static::$allowedTypes[$mimetype]);

            return new $classname($mimetype);

        } else {
            throw new UnsupportedMimeType($mimetype);
        }
    }

    public function getExtension()
    {
        return $this->_extension;
    }

    public function getMimetype()
    {
        return $this->_mimetype;
    }

    public function getType()
    {
        return $this->_type;
    }
}
