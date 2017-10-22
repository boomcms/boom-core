<?php

namespace BoomCMS\FileInfo\Drivers;

use FFMpeg\Exception\ExecutableNotFoundException;
use FFMpeg\FFProbe;

class Mpeg extends DefaultDriver
{
    public function getAssetType(): string
    {
        return 'audio';
    }

    /**
     * Extracts metadata via FFProbe.
     *
     * @return array
     */
    public function readMetadata(): array
    {
        try {
            $ffprobe = FFProbe::create();

            return $ffprobe
                ->format($this->readStream())
                ->all();
        } catch (ExecutableNotFoundException $e) {
            return [];
        }
    }
}
