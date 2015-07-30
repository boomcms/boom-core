<?php

namespace BoomCMS\Http\Controllers\Asset;

use Illuminate\Support\Facades\Response;

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
        $stream = fopen($this->asset->getFilename(), 'r');

        return Response::stream(function() use ($stream) {
            fpassthru($stream);
        }, 200, [
            'content-length' => $this->asset->getFilesize(),
            'content-type' => (string) $this->asset->getMimetype(),
        ]);
    }
}
