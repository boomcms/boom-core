<?php

namespace BoomCMS\Support;

use BoomCMS\Core\Asset;
use BoomCMS\Core\Page;
use BoomCMS\Core\Tag;
use BoomCMS\Support\Facades\Editor;
use BoomCMS\Support\Facades\Settings;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

abstract class Helpers
{
    /**
     * Returns the name of the theme used by the active page.
     *
     * @return string
     */
    public static function activeThemeName()
    {
        return Editor::getActivePage()->getTemplate()->getThemeName();
    }

    /**
     * If the app is in the production then the analytics setting is returned.
     * Otherwise an empty string is returned.
     *
     * @return string
     */
    public static function analytics()
    {
        return App::environment() === 'production' ? Settings::get('analytics') : '';
    }

    /**
     * Get the HTML code to embed an asset.
     *
     * @param Asset $asset
     *
     * @return string
     */
    public static function assetEmbed(Asset\Asset $asset, $height = null, $width = null)
    {
        return (string) $asset->getEmbedHtml($height, $width);
    }

    /**
     * Generate a URL to link to an asset.
     *
     * @param array $params
     *
     * @return string
     */
    public static function assetURL(array $params)
    {
        if (isset($params['asset']) && is_object($params['asset'])) {
            $asset = $params['asset'];
            $params['asset'] = $params['asset']->getId();
        }

        if (!isset($params['action'])) {
            $params['action'] = 'view';
        }

        if (isset($params['height']) && !isset($params['width'])) {
            $params['width'] = 0;
        }

        $url = route('asset', $params);

        if (isset($asset)) {
            $url .= '?'.$asset->getLastModified()->getTimestamp();
        }

        return $url;
    }

    /**
     * Get a count of assets matching an array of query parameters.
     *
     * @param array $params
     *
     * @return int
     */
    public static function countAssets(array $params)
    {
        return (new Asset\Query($params))->count();
    }

    /**
     * Get a count of pages matching an array of query parameters.
     *
     * @param array $params
     *
     * @return int
     */
    public static function countPages(array $params)
    {
        return (new Page\Query($params))->count();
    }

    /**
     * Returns an array of Pages which match the given query parameters.
     *
     * @param array $params
     *
     * @return array
     */
    public static function getAssets(array $params)
    {
        return (new Asset\Query($params))->getResults();
    }

    /**
     * Returns an array of Pages which match the given query parameters.
     *
     * @param array $params
     *
     * @return array
     */
    public static function getPages(array $params)
    {
        return (new Page\Query($params))->getResults();
    }

    /**
     * Get the next page in the sequence.
     *
     * @param array $params
     *
     * @return Page\Page
     */
    public static function next(array $params)
    {
        return (new Page\Query($params))
            ->getNextTo(Editor::getActivePage(), 'after');
    }

    /**
     * Get the previous page in a sequence.
     *
     * @param array $params
     *
     * @return Page\Page
     */
    public static function prev(array $params)
    {
        return (new Page\Query($params))
            ->getNextTo(Editor::getActivePage(), 'before');
    }

    /**
     * Get tags matching given parameters.
     * Accepts a page, group name, or tag as arguments in any order to search by.
     *
     *
     * @return array
     */
    public static function getTags()
    {
        $finder = new Tag\Finder\Finder();

        foreach (func_get_args() as $arg) {
            if (is_string($arg)) {
                $finder->addFilter(new Tag\Finder\Group($arg));
            } elseif ($arg instanceof Page\Page) {
                $finder->addFilter(new Tag\Finder\AppliedToPage($arg));
            } elseif ($arg instanceof Tag\Tag) {
                $finder->addFilter(new Tag\Finder\AppliedWith($arg));
            }
        }

        return $finder
            ->setOrderBy('group', 'asc')
            ->setOrderBy('name', 'asc')
            ->findAll();
    }

    /**
     * Get the pages applied to the children of a page.
     *
     * @param Page\Page $page
     * @param string    $group
     *
     * @return array
     */
    public static function getTagsInSection(Page\Page $page = null, $group = null)
    {
        $page = $page ?: Editor::getActivePage();

        $finder = new Tag\Finder\Finder();
        $finder->addFilter(new Tag\Finder\AppliedToPageDescendants($page));
        $finder->addFilter(new Tag\Finder\Group($group));

        return $finder->setOrderBy('name', 'asc')->findAll();
    }

    /**
     * Get a relative path to a file in a theme's public directory.
     *
     * @param string $file
     * @param string $theme
     *
     * @return string
     */
    public static function pub($file, $theme = null)
    {
        $theme = $theme ?: static::activeThemeName();

        return "/vendor/boomcms/themes/$theme/".ltrim(trim($file), '/');
    }

    public static function view($name, $data = [], $namespace = null)
    {
        $namespace = $namespace ?: static::activeThemeName();

        return View::make("$namespace::$name", $data);
    }
}
