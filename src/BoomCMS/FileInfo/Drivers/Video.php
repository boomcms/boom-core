<?php

namespace BoomCMS\FileInfo\Drivers;

use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Imagick;

class Video extends Mpeg
{
    public function getAssetType(): string
    {
        return 'video';
    }

    /**
     * Generates a thumbnail for the video from the first frame.
     *
     * @return Imagick
     */
    public function getThumbnail()
    {
        try {
            $ffmpeg = FFMpeg::create();
            $video = $ffmpeg->open($this->readStream());

            $frame = $video->frame(TimeCode::fromSeconds(0));
            $base64 = $frame->save(tempnam(sys_get_temp_dir(), basename($this->path)), true, true);

            return Imagick::readImageBlob($base64);
        } catch (Exception $e) {
            return;
        }
    }
}
