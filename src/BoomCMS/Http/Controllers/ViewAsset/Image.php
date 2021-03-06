<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

use BoomCMS\Contracts\Models\Asset;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use Illuminate\Http\Request;
use Intervention\Image\Constraint;
use Intervention\Image\ImageCache;
use Intervention\Image\ImageManager;

class Image extends BaseController
{
    /**
     * @var ImageManager
     */
    private $manager;

    protected $encoding;

    public function __construct(Request $request, Asset $asset)
    {
        parent::__construct($request, $asset);

        $this->manager = new ImageManager(['driver' => 'imagick']);
    }

    public function crop($width = null, $height = null)
    {
        if (empty($width) || empty($height)) {
            return parent::view();
        }

        $image = $this->manager->cache(function (ImageCache $cache) use ($width, $height) {
            return $cache->make(AssetFacade::path($this->asset))
                ->fit($width, $height)
                ->encode($this->encoding);
        });

        return $this->addHeaders($this->response)->setContent($image);
    }

    public function thumb($width = null, $height = null)
    {
        return $this->view($width, $height);
    }

    public function view($width = null, $height = null)
    {
        if (empty($width) && empty($height)) {
            return parent::view();
        }

        $image = $this->manager->cache(function (ImageCache $cache) use ($width, $height) {
            $width = empty($width) ? null : $width;
            $height = empty($height) ? null : $height;

            return $cache
                ->make(AssetFacade::path($this->asset))
                ->resize($width, $height, function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode($this->encoding);
        });

        return $this->addHeaders($this->response)->setContent($image);
    }
}
