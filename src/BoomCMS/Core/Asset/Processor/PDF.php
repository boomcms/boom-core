<?php

namespace BoomCMS\Core\Asset\Processor;

use Kohana;
use Imagick;

class PDF extends Processor
{
    public function thumbnail($width = null, $height = null)
    {
        $cacheKey = 'asset-' . $this->asset->getId() . '-thumb-' . (int) $width . '-' . (int) $height;

        if ( ! $image = Kohana::cache($cacheKey)) {
            $image = new Imagick($this->asset->getFilename() . '[0]');
            $image->setImageFormat('png');

            if ($width || $height) {
                $image->resizeImage($width, $height, Imagick::FILTER_UNDEFINED, 1);
            }

            $image = $image->getImageBlob();
            Kohana::cache($cacheKey, $image);
        }

        return $this->response
            ->headers('Content-type', 'image/jpg')
            ->body($image);
    }

    public function embed()
    {
        return $this->response
            ->body("<a class='b-asset-embed' href='/asset/view/{$this->asset->getId()}'>{$this->asset->getTitle()}</a>");
    }
}
