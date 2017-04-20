<?php

namespace BoomCMS\Http\Response;

use BoomCMS\Contracts\Models\Asset;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class Stream
{
    protected $asset;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    /**
     * This code has been adapted from the code at http://www.tuxxin.com/php-mp4-streaming.
     */
    public function getResponse()
    {
        $stream = AssetFacade::stream($this->asset);

        $size = $length = $this->asset->getFilesize();
        $start = 0;
        $end = $size - 1;
        $code = 200;

        if ($range = Request::header('Range')) {
            $c_start = $start;
            $c_end = $end;
            list(, $range) = explode('=', $range, 2);

            if (strpos($range, ',') !== false) {
                abort(416, null, ['Content-Range' => "bytes $start-$end/$size"]);
            }

            if ($range === '-') {
                $c_start = $size - substr($range, 1);
            } else {
                $range = explode('-', $range);
                $c_start = $range[0];
                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
            }

            $c_end = ($c_end > $end) ? $end : $c_end;

            if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
                abort(416, null, ['Content-Range' => "bytes $start-$end/$size"]);
            }

            $start = $c_start;
            $end = $c_end;
            $length = $end - $start + 1;
            fseek($stream, $start);
            $code = 206;
        }

        $headers = [
            'Content-Type'   => $this->asset->getMimetype(),
            'Content-Range'  => "bytes $start-$end/$size",
            'Content-Length' => $length,
            'Accept-Ranges'  => 'bytes',
        ];

        return Response::stream(function () use ($stream, $end) {
            $buffer = 1024 * 8;

            while (!feof($stream) && ($p = ftell($stream)) <= $end) {
                if ($p + $buffer > $end) {
                    $buffer = $end - $p + 1;
                }

                set_time_limit(0);
                echo fread($stream, $buffer);
                flush();
            }

//            if (is_resource($stream)) {
                fclose($stream);
//            }
        }, $code, $headers);
    }
}
