<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

use BoomCMS\Support\Facades\Asset;
use Intervention\Image\ImageCache;
use Intervention\Image\ImageManager;

class Pdf extends BaseController
{
    public function thumb($width = null, $height = null)
    {
        $manager = new ImageManager();

        if ($width && $height) {
            $image = $manager->cache(function (ImageCache $cache) use ($width, $height) {
                return $cache->make(Asset::thumbnail($this->asset))->fit($width, $height);
            });
        } else {
            $image = $manager->make(Asset::thumbnail($this->asset))->encode();
        }

        return $this->response
            ->header('content-type', 'image/png')
            ->setContent($image);
    }
}
