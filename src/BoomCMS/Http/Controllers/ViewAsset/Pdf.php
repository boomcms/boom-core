<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

use BoomCMS\Support\Facades\Asset;
use Intervention\Image\ImageManager;

class Pdf extends BaseController
{
    public function thumb($width = null, $height = null)
    {
        $manager = new ImageManager();

        if ($width && $height) {
            $image = $manager->cache(function ($manager) use ($width, $height) {
                return $manager->make(Asset::thumbnail($this->asset))->fit($width, $height);
            });
        } else {
            $image = $manager->make(Asset::thumbnail($this->asset))->encode();
        }

        return $this->response
            ->header('content-type', 'image/png')
            ->setContent($image);
    }
}
