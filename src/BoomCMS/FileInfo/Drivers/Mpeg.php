<?php

namespace BoomCMS\FileInfo\Drivers;

use FFMpeg\Exception\ExecutableNotFoundException;
use FFMpeg\FFProbe;

class Mpeg extends DefaultDriver
{
    public function readMetadata(): array
    {
        try {
            $ffprobe = FFProbe::create();

            $ffprobe
                ->format($this->getPathname())
                ->all();
        } catch (ExecutableNotFoundException $e) {
            return [];
        }
    }
}
