<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

use BoomCMS\Http\Response\Stream;
use Intervention\Image\ImageManager;

class Video extends BaseController
{
    public function thumb($width = null, $height = null)
    {
        if (!$this->asset->hasThumbnail()) {
            return parent::thumb();
        }

        $thumbnail = $this->asset->getThumbnail();
        $filename = $thumbnail->getFilename();
        $im = new ImageManager();

        if ($width && $height) {
            $image = $im->cache(function ($manager) use ($width, $height, $filename) {
                return $manager->make($filename)->fit($width, $height);
            });
        } else {
            $image = $im->make($filename)->encode();
        }

        return $this->response
                ->header('content-type', $thumbnail->getMimetype())
                ->setContent($image);
    }

    public function view($width = null, $height = null)
    {
        return (new Stream($this->asset))->getResponse();
    }
}
