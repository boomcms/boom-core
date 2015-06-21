<?php

namespace BoomCMS\Core\Http\Middleware;

use BoomCMS\Core\Page;
use BoomCMS\Core\Facades\Editor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class DefineGlobalViewSharedVariables
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        View::share('assetURL', function(array $params) {
            if ( !isset($params['action'])) {
                $params['action'] = 'view';
            }

            if (isset($params['height']) && !isset($params['width'])) {
                $params['width'] = 0;
            }

            return route('asset', $params);
        });

        View::share('request', $request);

        // TODO: Make $getPages(), $next, and $prev populate query params from request input.
        // E.g. get tag ID from query string etc.
        View::share('getPages', function(array $params) {
            return (new Page\Query($params))->getPages();
        });

        $editor = Editor::getFacadeRoot();

        View::share('next', function(array $params = []) use ($editor) {
            return (new Page\Query($params))->getNextTo($editor->getActivePage(), 'after');
        });

        View::share('prev', function(array $params = []) use ($editor) {
            return (new Page\Query($params))->getNextTo($editor->getActivePage(), 'before');
        });

        return $next($request);
    }
}