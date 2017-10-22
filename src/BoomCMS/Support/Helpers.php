<?php

namespace BoomCMS\Support;

use BoomCMS\Asset;
use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Contracts\Models\Tag as TagInterface;
use BoomCMS\Core\Tag;
use BoomCMS\Database\Models\Chunk;
use BoomCMS\Page;
use BoomCMS\Support\Facades\Chunk as ChunkFacade;
use BoomCMS\Support\Facades\Router;
use BoomCMS\Support\Facades\Settings;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

abstract class Helpers
{
    /**
     * Chunk cache.
     *
     * @var array
     */
    protected static $chunks = [];

    /**
     * Returns the name of the theme used by the active page.
     *
     * @return string
     */
    public static function activeThemeName()
    {
        return Router::getActivePage()->getTemplate()->getThemeName();
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
     * Interest a chunk into a page.
     *
     * @param string             $type
     * @param string             $slotname
     * @param PageInterface|null $page
     *
     * @return Chunk
     */
    public static function chunk($type, $slotname, $page = null)
    {
        return ChunkFacade::insert($type, $slotname, $page);
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
     * Reutrn the meta description for a page.
     *
     * If no page is given then the active page is used.
     *
     * The page description property will be used, if that isn't set then the page standfirst is used.
     *
     * @param null|Page $page
     *
     * @return string
     */
    public static function description(PageInterface $page = null)
    {
        $page = $page ?: Router::getActivePage();
        $description = $page->getDescription() ?:
            ChunkFacade::get('text', 'standfirst', $page)->text();

        return strip_tags($description);
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
            ->getNextTo(Router::getActivePage(), 'after');
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
            ->getNextTo(Router::getActivePage(), 'before');
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
            } elseif ($arg instanceof PageInterface) {
                $finder->addFilter(new Tag\Finder\AppliedToPage($arg));
            } elseif ($arg instanceof TagInterface) {
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
    public static function getTagsInSection(PageInterface $page = null, $group = null)
    {
        $page = $page ?: Router::getActivePage();

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
