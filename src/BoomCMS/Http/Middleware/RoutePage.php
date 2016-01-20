<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Routing\Router;
use Closure;
use Illuminate\Http\Request;

class RoutePage
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
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $this->router->routePage($request->route()->getParameter('location'));

        if ($response) {
            return $response;
        }

        return $next($request);
    }
}
