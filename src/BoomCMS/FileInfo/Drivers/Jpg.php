<?php

namespace BoomCMS\FileInfo\Drivers;

use Carbon\Carbon;
use Imagick;

class Jpg extends Image
{
    /**
     * {@inheritdoc}
     *
     * @return null|Carbon
     */
    public function getCreatedAt()
    {
        $metadata = $this->getMetadata();
        $keys = ['DateTimeOriginal', 'DateTimeDigitized', 'Date Time Digitized'];

        foreach ($keys as $key) {
            if (isset($metadata[$key])) {
                return $metadata[$key];
            }
        }
    }

    /**
     * Extracts EXIF data from an image.
     *
     * @return array
     */
    public function readMetadata(): array
    {
        $im = new Imagick($this->file->getPathname());
        $exif = $im->getImageProperties('exif:*');

        foreach ($exif as $key => $value) {
            $newKey = trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', str_replace('exif:', '', $key)));

            $exif[$newKey] = $value;
            unset($exif[$key]);
        }

        return $exif;
    }
}
