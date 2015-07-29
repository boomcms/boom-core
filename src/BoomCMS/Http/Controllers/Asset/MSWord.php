<?php

namespace BoomCMS\Http\Controllers\Asset;

class MSWord extends BaseController
{
    public function thumb($width = null, $height = null)
    {
        return $this->response
            ->header('Content-type', 'image/png')
            ->setContent(readfile(__DIR__.'/../../../../../public/img/ms_word.png'));
    }
}
