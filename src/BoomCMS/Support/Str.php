<?php

namespace BoomCMS\Support;

use Embera\Embera;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str as BaseStr;
use Rych\ByteSize;

abstract class Str extends BaseStr
{
    /**
     * Turn a number of bytes into a human friendly filesize.
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

    /**
     * Make links which include the current HTTP host relative, even if the scheme doens't match.
     *
     * Internal links within text are stored as relative links so that if a site moves host
     * or the database is copied to another site (e.g. development or staging versions)
     * the links will still work correctly.
     *
     * @param string $text
     *
     * @return string
     */
    public static function makeInternalLinksRelative($text)
    {
        if ($base = Request::getHttpHost()) {
            return preg_replace("|<(.*?)href=(['\"])(https?://)".$base."/(.*?)(['\"])(.*?)>|", '<$1href=$2/$4$5$6>', $text);
        }

        return $text;
    }

    /**
     * Replace embeddable URLs with the embed code.
     *
     * @param string $text
     *
     * @return string
     */
    public static function OEmbed($text)
    {
        $embera = new Embera();

        if ($data = $embera->getUrlInfo($text)) {
            $table = [];

            foreach ($data as $url => $service) {
                if (!empty($service['html'])) {
                    $table[$url] = $service['html'];
                }
            }

            foreach ($table as $url => $replacement) {
                $text = preg_replace('~(?<![\'\"])'.preg_quote($url).'(?![\'\"])(?!\</a\>)~', $replacement, $text);
            }
        }

        return $text;
    }

    /**
     * Embed storify links (doesn't use OEmbed).
     *
     * @param string $text
     *
     * @return string
     */
    public function storifyEmbed($text)
    {
        $matchString = "/\<p\>(https?\:\/\/(?:www\.)?storify\.com\/(?:[^\/]+)\/(?:[^\/]+))\/?\<\/p\>/i";
        $replaceString = '<script type="text/javascript" src="${1}.js"></script>';

        return \preg_replace($matchString, $replaceString, $text);
    }
}
