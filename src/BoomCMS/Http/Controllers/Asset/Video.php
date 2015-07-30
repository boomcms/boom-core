<?php

namespace BoomCMS\Http\Controllers\Asset;

class Video extends BaseController
{
    public function thumb($width = null, $height = null)
    {
        return $this->response
            ->header('Content-type', 'image/gif')
            ->setContent(readfile(__DIR__ . '/../../../../../public/img/icons/40x40/mov_icon.gif'));
    }

    public function view($width = null, $height = null)
    {
        return $this->response
            ->send_file($this->asset->getFilename(), $this->asset->getFilename(), [
                'inline' => true,
                'mime_type' => $this->asset->getMimetype(),
                'resumable' => true,
            ]);
    }
}
