<?php

namespace BoomCMS\Support;

use Illuminate\Support\Str as BaseStr;
use Rych\ByteSize;

abstract class Str extends BaseStr
{
    /**
     * Turn a number of bytes into a human friendly filesize
     *
     * @param int $bytes
     *
     * @return string
     */
    public static function filesize($bytes)
    { 
        $precision = (($bytes % 1024) === 0 || $bytes <= 1024) ? 0 : 1;

        $formatter = new ByteSize\Formatter\Binary();
        $formatter->setPrecision($precision);
        
        return $formatter->format($bytes);
    }
}
