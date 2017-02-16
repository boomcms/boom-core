<?php

namespace BoomCMS\FileInfo;

use BoomCMS\FileInfo\Contracts\FileInfoDriver;
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

    /**
     * Factory method for retrieving a FileInfo object.
     *
     * @param File $file
     *
     * @return FileInfoDriver
     */
    public function create(File $file): FileInfoDriver
    {
        $driver = $this->getDriver($file->getMimeType());
        $className = __NAMESPACE__.'\Drivers\\'.$driver;

        return new $className($file);
    }

    /**
     * Determines which driver to use for a given mimetype.
     *
     * @param string $mimetype
     *
     * @return string
     */
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
