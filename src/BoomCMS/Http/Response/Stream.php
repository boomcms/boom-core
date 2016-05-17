<?php

namespace BoomCMS\Http\Response;

use BoomCMS\Contracts\Models\Asset;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class Stream
{
    protected $asset;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    public function getResponse()
    {
        $size = $fullsize = $this->asset->getFilesize();
        $stream = fopen($this->asset->getFilename(), 'r');
        $code = 200;
        $headers = ['Content-type' => $this->asset->getMimetype()];

        if ($range = Request::header('Range')) {
            $eqPos = strpos($range, '=');
            $toPos = strpos($range, '-');
            $unit = substr($range, 0, $eqPos);
            $start = intval(substr($range, $eqPos + 1, $toPos));
            $success = fseek($stream, $start);

            if ($success == 0) {
                $size = $fullsize - $start;
                $code = 206;
                $headers['Accept-Ranges'] = $unit;
                $headers['Content-Range'] = $unit.' '.$start.'-'.($fullsize - 1).'/'.$fullsize;
            }
        }

        $headers['Content-Length'] = $size;

        return Response::stream(function () use ($stream) {
            fpassthru($stream);
        }, $code, $headers);
    }
}
