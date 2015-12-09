<?php

namespace BoomCMS\Support;

use Illuminate\Support\Facades\Config as ConfigFacade;
use Imagick;
use ImagickException;

abstract class File
{
    /**
     * Returns an array of 'interesting' exif data for a filepath.
     * 
     * If the file at the given path isn't supported then an empt array is returned.
     *
     * @param string $path
     *
     * @return array
     */
    public static function exif($path)
    {
        try {
            $im = new Imagick($path);
            $exif = $im->getImageProperties('exif:*');
        } catch (ImagickException $e) {
            return [];
        }

        foreach ($exif as $key => $value) {
            $newKey = str_replace('exif:', '', $key);
            $newKey = preg_replace('/(?<!\ )[A-Z]/', ' $0', $newKey);

            $exif[$newKey] = $value;
            unset($exif[$key]);
        }

        return $exif;
    }

    /**
     * Determines the file extension from a filename and mimetype.
     *
     * @param string $mimetype
     * @param string $filename
     *
     * @return string
     */
    public static function extension($filename, $mimetype)
    {
        preg_match('|\.([a-z]+)$|', $filename, $extension);

        if (isset($extension[1])) {
            $extension = $extension[1];
        } else {
            $extension = static::extensionFromMimetype($mimetype);
        }

        return $extension;
    }

    /**
     * Returns an extension which can be used for a particular mimetype.
     *
     * Used to determine the extension for a file when it's not present in the filename.
     *
     * @param string $mimetype
     *
     * @return string
     */
    public static function extensionFromMimetype($mimetype)
    {
        $extensions = ConfigFacade::get('boomcms.assets.extensions');

        return array_search($mimetype, $extensions);
    }
}
