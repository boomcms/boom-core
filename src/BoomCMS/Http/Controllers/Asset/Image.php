<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Contracts\Models\Asset;
use Intervention\Image\ImageManager;

class Image extends BaseController
{
    /**
     * @var ImageManager
     */
    private $manager;

    protected $encoding;

    public function __construct(Asset $asset)
    {
        parent::__construct($asset);

        $this->manager = new ImageManager(['driver' => 'imagick']);
    }

    public function crop($width = null, $height = null)
    {
        if ($width && $height) {
            $image = $this->manager->cache(function ($manager) use ($width, $height) {
                return $manager->make($this->asset
                    ->getFilename())
                    ->fit($width, $height)
                    ->encode($this->encoding);
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
        $filename = $this->asset->exists() ? $this->asset->getFilename() : __DIR__.'/../../../../vendor/boomcms/boom-core/img/placeholder.png';

        if ($width || $height) {
            $image = $this->manager->cache(function ($manager) use ($width, $height, $filename) {
                return $manager->make($filename)->resize($width != 0 ? $width : null, $height != 0 ? $height : null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode($this->encoding);
            });
        } else {
            $image = $this->manager->make($this->asset->getFilename())->encode();
        }

        return $this->response
            ->header('content-type', $this->asset->getMimetype())
            ->setContent($image);
    }
}
