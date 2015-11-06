<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Core\Asset\PdfThumbnail;
use Intervention\Image\ImageManager;

class Pdf extends BaseController
{
    public function thumb($width = null, $height = null)
    {
        $manager = new ImageManager();
        $thumb = new PdfThumbnail($this->asset);

        if ($width && $height) {
            $image = $manager->cache(function ($manager) use ($width, $height, $thumb) {
                return $manager->make($thumb->getAndMakeFilename())->fit($width, $height);
            });
        } else {
            $image = $manager->make($thumb->getAndMakeFilename())->encode();
        }

        return $this->response
                ->header('content-type', 'image/png')
                ->setContent($image);
    }
}
