<?php

namespace BoomCMS\Core\Asset\Type;

use BoomCMS\Core\Asset\Asset;
use Imagick;

class PDF extends Asset
{
    public function getThumbnailFilename()
    {
        $filename = $this->getFilename() . '.thumb';

        if ( ! file_exists($filename)) {
            $image = new Imagick($this->getFilename() . '[0]');
            $image->setImageFormat('png');

            file_put_contents($filename, $image->getImageBlob());
        }

        return $filename;
    }

    public function getType()
    {
        return "PDF";
    }
}
