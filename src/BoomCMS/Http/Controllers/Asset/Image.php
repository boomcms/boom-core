<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Contracts\Models\Asset;
use Intervention\Image\Constraint;
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
        if (!empty($width) && !empty($height)) {
            $image = $this->manager->cache(function (ImageManager $manager) use ($width, $height) {
                return $manager->make($this->asset->getFilename())
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
        if (!empty($width) || !empty($height)) {
            $image = $this->manager->cache(function (ImageManager $manager) use ($width, $height) {
                $width = empty($width) ? null : $width;
                $height = empty($height) ? null : $height;

                return $manager
                    ->make($this->asset->getFilename())
                    ->resize($width, $height, function (Constraint $constraint) {
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
