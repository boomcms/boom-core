<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

use BoomCMS\Asset\ImageCache;
use BoomCMS\Contracts\Models\Asset;
use Illuminate\Http\Request;

class Image extends BaseController
{
    /**
     * @var ImageCache
     */
    protected $cache;

    protected $encoding;

    public function __construct(Request $request, Asset $asset, ImageCache $cache)
    {
        parent::__construct($request, $asset);

        $this->cache = $cache;

        if (empty($this->encoding) === false) {
            $this->cache->setEncoding($this->encoding);
        }
    }

    public function crop($width = null, $height = null)
    {
        if (empty($width) || empty($height)) {
            return parent::view();
        }

        $image = $this->cache->crop($this->asset, $width, $height);

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

        $image = $this->cache->resize($this->asset, $width, $height);

        return $this->addHeaders($this->response)->setContent($image);
    }
}
