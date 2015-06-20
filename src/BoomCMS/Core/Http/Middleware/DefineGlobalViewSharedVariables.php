<?php

namespace BoomCMS\Core\Http\Middleware;

use BoomCMS\Core\Page;
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

        View::share('getPages', function(array $params) {
            return (new Page\Query($params))->getPages();
        });

        return $next($request);
    }
}