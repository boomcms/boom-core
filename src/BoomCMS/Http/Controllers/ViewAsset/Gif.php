<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

/**
 * Gifs are treated differently to other images.
 *
 * They are not resized as resizing breaks animated gifs.
 */
class Gif extends BaseController
{
    public function crop($width = null, $height = null)
    {
        return $this->view($width, $height);
    }

    public function thumb($width = null, $height = null)
    {
        return $this->view();
    }
}
