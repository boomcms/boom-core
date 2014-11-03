<?php

use \Boom\Editor\Editor as Editor;

abstract class Boom_Core
{
    public static function include_css()
    {
        $css = Kohana::$config->load('media')->get('css');

        $assets = Assets::factory('cms_css');
        foreach ($css as $c) {
            $assets->css($c);
        }

        return $assets;
    }

    public static function include_js()
    {
        $core_js = Kohana::$config->load('media')->get('corejs');
        $js = Kohana::$config->load('media')->get('js');

        $assets = Assets::factory('cms_js');

        foreach ($core_js as $j) {
            $assets->js($j);
        }

        foreach ($js as $j) {
            $assets->js($j);
        }

        return $assets;
    }

    public static function page_format(Request $request)
    {
        // Change the controller action depending on the request accept header.
        $accepts = $request->accept_type();

        foreach (array_keys($accepts) as $accept) {
            switch ($accept) {
                case 'application/json':
                    return 'json';
                case 'application/rss+xml':
                    return 'rss';
                    break;
                case 'text/html':
                    return 'html';
                case '*/*':
                    return 'html';
            }
        }

        throw new HTTP_Exception_406();
    }

    public static function process_uri(Route $route, array $params, Request $request)
    {
        if (substr($params['location'], 0, 1) == '_') {
            $page = \Boom\Page\ShortURL::pageFromUrl($params['location']);
        } else {
            $page_url = new Model_Page_URL(['location' => $params['location']]);

            if ( ! $page_url->loaded()) {
                return false;
            }

            $page = \Boom\Page\Factory::byId($page_url->page_id);
        }

        if ($page->loaded()) {
            // If the page has been deleted then return 410.
            if ($page->isDeleted()) {
                throw new HTTP_Exception_410();
            }

            if (Editor::instance()->isDisabled() && ! $page->isVisible()) {
                return false;
            }

            if ( ! isset($page_url) || ! $page_url->is_primary) {
                header('Location: '.$page->url(), null, 301);
                exit;
            }

            // Change the page format depending on the request headers.
            $format = (isset($params['format'])) ? $params['format'] : Boom::page_format($request);

            // The URI matches a page in the CMS so we're going to process it with the Page controller.
            $template_controller = 'Page_'.ucfirst($format).'_'.$page->getTemplate()->getControllerName();

            $controller = (class_exists('Controller_'.$template_controller)) ? $template_controller : 'Page_'.ucfirst($format);
            $params['controller'] = $controller;
            $params['action'] = 'show';

            // Add the page model as a paramater for the controller.
            $params['page'] = $page;

            return $params;
        }

        return false;
    }
}
