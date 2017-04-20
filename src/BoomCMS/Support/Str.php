<?php

namespace BoomCMS\Support;

use Caxy\HtmlDiff\HtmlDiff;
use Closure;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str as BaseStr;
use Rych\ByteSize;

abstract class Str extends BaseStr
{
    public static function diff($old, $new)
    {
        $htmlDiff = new HtmlDiff($old, $new);

        return $htmlDiff->build();
    }

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
     * Convert a filename to a name.
     *
     * Used for getting the name of a template from a filename.
     *
     * @param string $filename
     *
     * @return string
     */
    public static function filenameToTitle($filename)
    {
        return ucwords(str_replace(['_', '-'], ' ', $filename));
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
            return preg_replace("~<(.*?)(href|src)=(['\"])(https?://)".$base."/(.*?)(['\"])(.*?)>~", '<$1$2=$3/$5$6$7>', $text);
        }

        return $text;
    }

    /**
     * Adds paragraph HTML tags to text treating each new line as a paragraph break.
     *
     * @param string $text
     *
     * @return string
     */
    public static function nl2paragraph($text)
    {
        $paragraphs = explode("\n", $text);

        foreach ($paragraphs as &$paragraph) {
            $paragraph = "<p>$paragraph</p>";
        }

        return implode('', $paragraphs);
    }

    /**
     * Embed storify links (doesn't use oEmbed).
     *
     * @param string $text
     *
     * @return string
     */
    public static function storifyEmbed($text)
    {
        $matchString = "/\<p\>(https?\:\/\/)?(?:www\.)?(storify\.com\/(?:[^\/]+)\/(?:[^\/]+))\/?\<\/p\>/im";
        $replaceString = '<div class="storify"><iframe src="//$2/embed?border=false" allowtransparency="true"></iframe><script src="//$2.js?border=false"></script><noscript>[<a href="//$2" target="_blank">View on Storify</a>]</noscript></div>';

        return \preg_replace($matchString, $replaceString, $text);
    }

    /**
     * Make a string unique.
     *
     * Increments a numeric suffix until the given closure returns true.
     *
     * @param string  $initial
     * @param Closure $closure
     *
     * @return string
     */
    public static function unique($initial, Closure $closure)
    {
        $append = 0;

        do {
            $string = ($append > 0) ? ($initial.$append) : $initial;
            $append++;
        } while ($closure($string) === false);

        return $string;
    }
}
