<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

use Illuminate\Http\Response;

class Gif extends BaseController
{
    /**
     * Gifs are not resized to ensure that animated gifs animate.
     *
     * Width and height parameters are therefore ignored.
     *
     * @param int $width
     * @param int $height
     *
     * @return Response
     */
    public function view($width = null, $height = null)
    {
        return $this->response
            ->header('content-type', $this->asset->getMimetype())
            ->setContent(file_get_contents($this->asset->getFilename()));
    }

    public function thumb($width = null, $height = null)
    {
        return $this->view();
    }
}
