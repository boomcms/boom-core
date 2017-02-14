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
}
