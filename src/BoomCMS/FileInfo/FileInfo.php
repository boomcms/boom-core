<?php

namespace BoomCMS\FileInfo;

use Symfony\Component\HttpFoundation\File\File;

class FileInfo
{
    public function create(File $file)
    {
        $driver = $this->getDriver($file);
        $className = __NAMESPACE__.'\Driver\\'.$driver;

        return new $className($file);
    }

    public function getDriver(File $file)
    {
        $mimetype = $file->getMimeType();

        if (strpos($mimetype, 'video') === 0 || strpos($mimetype, 'audio') === 0) {
            return 'Mpeg';
        }

        switch ($mimetype) {
            case 'image/jpeg':
                return 'Jpg';
            case 'application/pdf':
                return 'Pdf';
            case 'image/svg+xml':
                return 'Svg';
            default:
                return 'DefaultDriver';
        }
    }
}
