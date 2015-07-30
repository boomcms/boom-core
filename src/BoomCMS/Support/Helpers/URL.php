<?php

namespace BoomCMS\Support\Helpers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

/**
 * Helper functions for page URLs
 *
 */
abstract class URL
{
    /**
	 * Generate a unique URL from a page title
	 *
	 * @param string $base
	 * @param string $title
	 */
    public static function fromTitle($base, $title)
    {
        $url = static::sanitise($title);

        // If the base URL isn't empty and there's no trailing / then add one.
        if ($base && substr($base, -1) != "/") {
            $base = $base."/";
        }

        $url = ($base == '/') ? $url : $base.$url;

        return static::makeUnique($url);
    }

    /**
     * Determine whether a URL is already being used by a page in the CMS
     *
     * @param string $url
     */
    public static function isAvailable($url, $ignore_url = null)
    {
        $query = DB::table('page_urls')
            ->select(DB::raw('1'))
            ->where('location', '=', $url);

        if ($ignore_url) {
            $query->where('id', '!=', $ignore_url);
        }

        $result = $query->first();

        return $result === null;
    }

    public static function makeRelative($url)
    {
        return ($base = Request::getHttpHost()) ? str_replace(Request::getScheme() . $base, '/', $url) : $url;
    }

    /**
	 * Increments a numeric suffix until the URL is unique
	 *
	 * @param string $url
	 */
    public static function makeUnique($url)
    {
        $append = 0;
        $start_url = $url;

        do {
            $url = ($append > 0) ? ($start_url.$append) : $start_url;
            $append++;
        } while ( ! static::isAvailable($url));

        return $url;
    }

    /**
	 * Remove invalid characters from a URL
	 *
	 * @param string $url
	 */
    public static function sanitise($url)
    {
        $url = trim($url);
        $url = strtolower($url);
        $url = parse_url($url, PHP_URL_PATH);        // Make sure it doesn't contain a hostname
        $url = preg_replace('|/+|', '/', $url);        // Remove duplicate forward slashes.

        if ($url !== '/') {
            // Remove trailing or preceeding slashes
            $url = trim($url, '/');
        }

        $url = preg_replace('|[^'.preg_quote('-').'\/\pL\pN\s]+|u', '', $url); // Remove all characters that are not a hypeh, letters, numbers, or forward slash
        $url = preg_replace('|['.preg_quote('-').'\s]+|u', '-', $url); // Replace all hypens and whitespace by a single hyphen

        return $url;
    }
}
