<?php

namespace BoomCMS\Support\Helpers;

use BoomCMS\Contracts\Models\Site;
use BoomCMS\Support\Facades\URL as URLFacade;
use Illuminate\Support\Facades\Request;

/**
 * Helper functions for page URLs.
 */
abstract class URL
{
    /**
     * Generate a unique URL from a page title.
     *
     * @param string $base
     * @param string $title
     */
    public static function fromTitle(Site $site, $base, $title)
    {
        $url = static::sanitise($title);

        // If the base URL isn't empty and there's no trailing / then add one.
        if ($base && substr($base, -1) != '/') {
            $base = $base.'/';
        }

        $url = ($base == '/') ? $url : $base.$url;

        return static::makeUnique($site, $url);
    }

    public static function makeRelative($url)
    {
        return preg_replace('|^https?://'.Request::getHttpHost().'|', '', $url);
    }

    /**
     * Increments a numeric suffix until the URL is unique.
     *
     * @param Site   $site
     * @param string $url
     *
     * @return string
     */
    public static function makeUnique(Site $site, $url)
    {
        $append = 0;
        $startUrl = $url;

        do {
            $url = ($append > 0) ? ($startUrl.$append) : $startUrl;
            $append++;
        } while (!URLFacade::isAvailable($site, $url));

        return $url;
    }

    /**
     * Remove invalid characters from a URL.
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
