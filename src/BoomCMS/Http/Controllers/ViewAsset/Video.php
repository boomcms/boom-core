<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

use BoomCMS\Http\Response\Stream;

class Video extends BaseController
{
    public function view($width = null, $height = null)
    {
        return (new Stream($this->asset))->getResponse();
    }
}
