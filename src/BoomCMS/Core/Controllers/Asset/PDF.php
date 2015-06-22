<?php

namespace BoomCMS\Core\Controllers\Asset;

use Intervention\Image\ImageManager;

class PDF extends BaseController
{
    public function thumb($width = null, $height = null)
    {
        $manager = new ImageManager();

        if ($width && $height) {
            $image = $manager->cache(function ($manager) use ($width, $height) {
                return $manager->make($this->asset->getThumbnailFilename())->fit($width, $height);
            });
        } else {
            $image = $manager->make($this->asset->getThumbnailFilename())->encode();
        }

        return $this->response
                ->header('content-type', 'image/png')
                ->setContent($image);
    }

    public function embed()
    {
        return "<a class='b-asset-embed' href='/asset/view/{$this->asset->getId()}'>{$this->asset->getTitle()}</a>";
    }
}
