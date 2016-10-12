<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

use BoomCMS\Http\Response\Stream;
use Intervention\Image\ImageManager;

class Video extends BaseController
{
    public function view($width = null, $height = null)
    {
        return (new Stream($this->asset))->getResponse();
    }
}
