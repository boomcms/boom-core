<?php

namespace BoomCMS\FileInfo;

use Symfony\Component\HttpFoundation\File\File;

class FileInfo
{
    protected $byMimetype = [
        'image/jpeg'                                                              => 'Jpg',
        'application/pdf'                                                         => 'Pdf',
        'image/svg+xml'                                                           => 'Svg',
        'application/msword'                                                      => 'Word',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word',
    ];

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

        foreach ($this->byMimetype as $match => $driver) {
            if ($mimetype === $match) {
                return $driver;
            }
        }

        if (strpos($mimetype, 'image') === 0) {
            return 'Image';
        }

        return 'DefaultDriver';
    }
}
