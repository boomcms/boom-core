<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Routing\Router;
use Closure;
use Illuminate\Http\Request;

class RouteSite
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->router->routeHostname($request->getHttpHost());

        return $next($request);
    }
}
