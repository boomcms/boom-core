<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Routing\Router;
use Closure;
use Illuminate\Http\Request;

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
        $response = $router->process($request->route()->getParameter('location'));

        if ($response) {
            return $response;
        }

        return $next($request);
    }
}
