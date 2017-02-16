<?php

namespace BoomCMS\FileInfo\Drivers;

use Carbon\Carbon;
use Imagick;

class Jpg extends Image
{
    public function getCreatedAt()
    {
        $metadata = $this->getMetadata();

        return isset($metadata['DateTimeOriginal']) ?
            Carbon::parse($metadata['DateTimeOriginal']) : null;
    }

    public function readMetadata(): array
    {
        $im = new Imagick($this->file->getPathname());
        $exif = $im->getImageProperties('exif:*');

        foreach ($exif as $key => $value) {
            $newKey = preg_replace('/(?<!\ )[A-Z]/', ' $0', str_replace('exif:', '', $key));

            $exif[$newKey] = $value;
            unset($exif[$key]);
        }

        return $exif;
    }
}
