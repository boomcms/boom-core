<?php

namespace BoomCMS\Support;

abstract class File
{
    /**
     * Returns the mimetype for a file at a given path
     *
     * @param string $path
     *
     * @return false|string
     */
    public static function mime($path)
    {
        if (!file_exists($path) || !is_readable($path) || is_dir($path)) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $path);
        finfo_close($finfo);

        return $mime;
    }
}