<?php

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Support\Facades\Router;
use BoomCMS\Support\Helpers;

if (!function_exists('meta_keywords')) {
    function meta_keywords(Page $page = null)
    {
        $page = $page ?: Router::getActivePage();

        return "<meta name='keywords' content='{$page->getKeywords()}'>";
    }
}

if (!function_exists('meta_description')) {
    function meta_description(Page $page = null)
    {
        $description = Helpers::description($page);

        return "<meta name='description' content='$description'>";
    }
}

if (!function_exists('meta_robots')) {
    function meta_robots(Page $page = null)
    {
        $page = $page ?: Router::getActivePage();
        $content = ($page->allowsExternalIndexing()) ? 'index, follow' : 'noindex, nofollow';

        return "<meta name='robots' content='$content'>";
    }
}

if (!function_exists('meta_og')) {
    function meta_og(Page $page = null)
    {
        $page = $page ?: Router::getActivePage();

        $siteName = Settings::get('site.name');
        $description = Helpers::description($page);

        $html = "<meta property='og:type' content='website'>
            <meta property='og:site_name' content='$siteName'>
            <meta property='og:url' content='{$page->url()}'>
            <meta property='og:title' content='{$page->getTitle()}'>
            <meta property='og:description' content='$description'>";

        if ($page->hasFeatureImage()) {
            $url = URL::route('asset', ['asset' => $page->getFeatureImage()]);

            $html .= "<meta property='og:image' content='$url'>";
        }

        return $html;
    }
}

if (!function_exists('h1')) {
    function h1(Page $page = null)
    {
        $page = $page ?: Router::getActivePage();

        return "<h1 id='b-page-title'>{$page->getTitle()}</h1>";
    }
}
