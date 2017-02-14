<?php

namespace BoomCMS\FileInfo\Drivers;

use FFMpeg\FFProbe;

class Mpeg extends Image
{
    public function readMetadata()
    {
        $ffprobe = FFProbe::create();

        $ffprobe
            ->format($this->getPathname())
            ->all();
    }
}
