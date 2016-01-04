<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Routing\Router;
use BoomCMS\Support\Facades\Editor;
use BoomCMS\Support\Facades\Page;
use BoomCMS\Support\Facades\URL;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class RouteRequest
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, Router $router)
    {
        $router = $router->process($request);

        return $next($request);
    }
}
