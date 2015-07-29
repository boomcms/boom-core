<?php

namespace BoomCMS\Http\Controllers\Asset;

class MSWord extends BaseController
{
    public function thumb($width = null, $height = null)
    {
        return $this->response
            ->header('Content-type', 'image/jpg')
            ->setContent(readfile(__DIR__.'/../../../../../public/img/icons/ms_word.jpg'));
    }
}
