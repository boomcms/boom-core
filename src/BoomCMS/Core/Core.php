<?php

use BoomCMS\Core\Editor\Editor as Editor;

abstract class Boom_Core
{
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

            $params['format'] = isset($params['format']) ? $params['format'] : Boom::page_format($request);
            $params['controller'] = 'Page_' . ucfirst(strtolower($params['format']));
            $params['action'] = 'show';
            $params['page'] = $page;

            return $params;
        }

        return false;
    }
}
