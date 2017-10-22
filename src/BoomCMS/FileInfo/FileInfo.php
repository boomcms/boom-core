<?php

namespace BoomCMS\FileInfo;

use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use Illuminate\Filesystem\FilesystemAdapter;

class FileInfo
{
    protected $byMimetype = [
        'application/pdf'                                                         => 'Pdf',
        'image/svg+xml'                                                           => 'Svg',
        'application/msword'                                                      => 'Word',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word',
    ];

    protected $byMimeStart = [
        'video' => 'Video',
        'audio' => 'Mpeg',
        'image' => 'Image',
    ];

    /**
     * Factory method for retrieving a FileInfo object.
     *
     * @param File $file
     *
     * @return FileInfoDriver
     */
    public function create(FilesystemAdapter $filesystem, string $path): FileInfoDriver
    {
        $driver = $this->getDriver($filesystem->mimeType($path));
        $className = __NAMESPACE__.'\Drivers\\'.$driver;

        return new $className($filesystem, $path);
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
        foreach ($this->byMimetype as $match => $driver) {
            if ($mimetype === $match) {
                return $driver;
            }
        }

        foreach ($this->byMimeStart as $match => $driver) {
            if (strpos($mimetype, $match) === 0) {
                return $driver;
            }
        }

        return 'DefaultDriver';
    }
}
