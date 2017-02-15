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

            return $ffprobe
                ->format($this->file->getPathname())
                ->all();
        } catch (ExecutableNotFoundException $e) {
            return [];
        }
    }
}
