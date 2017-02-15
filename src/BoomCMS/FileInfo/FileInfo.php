<?php

namespace BoomCMS\FileInfo;

use Symfony\Component\HttpFoundation\File\File;

class FileInfo
{
    public function create(File $file)
    {
        $driver = $this->getDriver($file->getMimeType());
        $className = __NAMESPACE__.'\Drivers\\'.$driver;

        return new $className($file);
    }

    public function getDriver(string $mimetype)
    {
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
        }

        if (strpos($mimetype, 'image') === 0) {
            return 'Image';
        }

        return 'DefaultDriver';
    }
}
