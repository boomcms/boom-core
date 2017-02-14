<?php

namespace BoomCMS\FileInfo\Drivers;

use FFMpeg\FFProbe;

class Jpg extends Image
{
    public function readMetadata()
    {
        $ffprobe = FFProbe::create();

        $ffprobe
            ->format($this->getPathname())
            ->all(); 
    }
}
