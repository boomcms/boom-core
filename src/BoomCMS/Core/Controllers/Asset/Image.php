<?php

namespace BoomCMS\Core\Controllers\Asset;

use BoomCMS\Core\Auth;
use BoomCMS\Core\Asset\Asset;

use Intervention\Image\ImageManager;

class Image extends BaseController
{
    /**
     *
     * @var ImageManager
     */
    private $manager;

    public function __construct(Auth\Auth $auth, Asset $asset)
    {
        parent::__construct($auth, $asset);

        $this->manager = new ImageManager();
    }

    public function crop($width = null, $height = null)
    {
        if ($width && $height) {
            $image = $this->manager->cache(function ($manager) use ($width, $height) {
                return $manager->make($this->asset->getFilename())->fit($width, $height);
            });
        } else {
            $image = $this->manager->make($this->asset->getFilename())->encode();
        }

        return $this->response
                ->header('content-type', $this->asset->getMimetype())
                ->setContent($image);
    }

    public function thumb($width = null, $height = null)
    {
        return $this->view($width, $height);
    }

    public function view($width = null, $height = null)
    {
        $filename = $this->asset->exists() ? $this->asset->getFilename() : __DIR__ . '/../../../../vendor/boomcms/boom-core/img/placeholder.png';

        if ($width || $height) {
            $image = $this->manager->cache(function ($manager) use ($width, $height, $filename) {
                return $manager->make($filename)->resize($width != 0 ? $width : null, $height != 0 ? $height : null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            });
        } else {
            $image = $this->manager->make($this->asset->getFilename())->encode();
        }

        return $this->response
            ->header('content-type', (string) $this->asset->getMimetype())
            ->setContent($image);
    }

    public function embed()
    {
       return "<img src='/asset/view/{$this->asset->getId()}' />";

    }
}
