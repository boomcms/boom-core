<?php

namespace BoomCMS\Core\URL;

use BoomCMS\Contracts\Models\Page;

/*
 * Page short URLs similar to t.co etc.
 * A short URLL is the page ID converted to base-36 and prefixed with an underscore.
 * We prefix the short URLs to avoid the possibility of conflicts with real URLs
 */
abstract class ShortURL
{
    public static function urlFromPage(Page $page)
    {
        return '_'.base_convert($page->getId(), 10, 36);
    }

    public static function pageFromUrl($url)
    {
        $page_id = base_convert(substr($url, 1), 36, 10);

        return Finder::byId($page_id);
    }
}
